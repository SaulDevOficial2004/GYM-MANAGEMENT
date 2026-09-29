<?php

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/response.php';

const IDLE_TIMEOUT = 1800;
const ABSOLUTE_TIMEOUT = 43200;
const ID_ROTATION_INTERVAL = 900;

function apiError(
    string $message,
    int $statusCode = 401
): never {

    http_response_code($statusCode);

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);

    exit();

}

function requireApiLogin(): void
{
    if (
        !isset($_SESSION['user_id'])
        || !isset($_SESSION['telefono'])
        || !isset($_SESSION['rol_id'])
        || !isset($_SESSION['rol_nombre'])
    ) {

        apiError(
            'Sesión no válida o expirada',
            401
        );

    }

    requireCsrf();
    checkSessionExpiration();
    enforceActiveApiUserSession();
}

function checkSessionExpiration(): void
{
    $now = time();

    if (!isset($_SESSION['login_time'])) {
        destroyCurrentApiSession();
        apiError('Sesión no iniciada correctamente', 401);
    }

    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = $now;
    }

    if (($now - (int) $_SESSION['last_activity']) > IDLE_TIMEOUT) {
        destroyCurrentApiSession();
        apiError('Sesión expirada por inactividad', 401);
    }

    if (($now - (int) $_SESSION['login_time']) > ABSOLUTE_TIMEOUT) {
        destroyCurrentApiSession();
        apiError('Sesión expirada por tiempo excedido', 401);
    }

    if (!isset($_SESSION['id_rotado']) || ($now - (int) $_SESSION['id_rotado']) > ID_ROTATION_INTERVAL) {
        session_regenerate_id(true);
        $_SESSION['id_rotado'] = $now;
    }

    $_SESSION['last_activity'] = $now;
}

function destroyCurrentApiSession(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function isCurrentApiUserActive(): bool
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    global $connect;

    if (!isset($connect) || !($connect instanceof mysqli)) {
        return false;
    }

    $sql = "
        SELECT activo
        FROM usuarios
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $connect->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $userId = (int) $_SESSION['user_id'];

    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    return is_array($user) && (int) $user['activo'] === 1;
}

function enforceActiveApiUserSession(): void
{
    //VALIDACION USUARIO
    if (!isCurrentApiUserActive()) {
        destroyCurrentApiSession();

        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'status' => 'error',
            'success' => false,
            'force_logout' => true,
            'message' => 'Tu usuario fue desactivado. La sesión se cerrará.'
        ]);

        exit();
    }
}

function requireApiRoles(
    array $rolesPermitidos
): void {

    requireApiLogin();

    requireCsrf();

    $rolActual = strtolower(
        trim($_SESSION['rol_nombre'])
    );

    $rolesNormalizados = array_map(

        fn(string $rol): string => strtolower(
            trim($rol)
        ),

        $rolesPermitidos

    );

    if (!in_array(
        $rolActual,
        $rolesNormalizados,
        true
    )) {

        apiError(
            'No tienes permisos para realizar esta operación',
            403
        );

    }
}

function apiCurrentUserId(): int
{
    requireApiLogin();

    return (int) $_SESSION['user_id'];
}

function apiCurrentUserRole(): string
{
    requireApiLogin();

    return (string) $_SESSION['rol_nombre'];
}
