<?php

require_once __DIR__ . '/../php_action/conn_db.php';

const IDLE_TIMEOUT = 1800;
const ABSOLUTE_TIMEOUT = 43200;
const ID_ROTATION_INTERVAL = 900;

function requireLogin(): void
{
    if (
        !isset($_SESSION['user_id'])
        || !isset($_SESSION['telefono'])
        || !isset($_SESSION['rol_id'])
        || !isset($_SESSION['rol_nombre'])
    ) {

        header('Location: index.php');

        exit();

    }

    checkSessionExpiration();
    enforceActiveUserSession();
}

function checkSessionExpiration(): void
{
    $now = time();

    if (!isset($_SESSION['login_time'])) {
        destroyCurrentSession();
        header('Location: index.php');
        exit();
    }

    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = $now;
    }

    if (($now - (int) $_SESSION['last_activity']) > IDLE_TIMEOUT) {
        destroyCurrentSession();
        header('Location: index.php?session=expired');
        exit();
    }

    if (($now - (int) $_SESSION['login_time']) > ABSOLUTE_TIMEOUT) {
        destroyCurrentSession();
        header('Location: index.php?session=expired');
        exit();
    }

    if (!isset($_SESSION['id_rotado']) || ($now - (int) $_SESSION['id_rotado']) > ID_ROTATION_INTERVAL) {
        session_regenerate_id(true);
        $_SESSION['id_rotado'] = $now;
    }

    $_SESSION['last_activity'] = $now;
}

function destroyCurrentSession(): void
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

function isCurrentUserActive(): bool
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

function enforceActiveUserSession(): void
{
    //VALIDACION USUARIO
    if (!isCurrentUserActive()) {
        destroyCurrentSession();

        header('Location: index.php?session=disabled');

        exit();
    }
}

function requireRoles(array $rolesPermitidos): void
{
    requireLogin();

    $rolActual = strtolower(
        trim($_SESSION['rol_nombre'])
    );

    $rolesPermitidos = array_map(

        fn($rol) => strtolower(
            trim($rol)
        ),

        $rolesPermitidos

    );

    if (!in_array(
        $rolActual,
        $rolesPermitidos,
        true
    )) {

        header('Location: pagina.php');

        exit();

    }
}

function isRole(string $rol): bool
{
    if (!isset($_SESSION['rol_nombre'])) {

        return false;

    }

    return strtolower(
        trim($_SESSION['rol_nombre'])
    ) === strtolower(
        trim($rol)
    );
}

function currentUserId(): ?int
{
    return isset($_SESSION['user_id'])
        ? (int) $_SESSION['user_id']
        : null;
}

function currentUserRole(): ?string
{
    return $_SESSION['rol_nombre'] ?? null;
}
