<?php

require_once __DIR__ . '/includes/auth.php';

requireRoles([
    'Administrador',
    'Dueño'
]);

require_once __DIR__ . '/php_action/conn_db.php';

$sqlRecepcionistas = "
    SELECT
        u.id,
        u.nombre,
        u.telefono,
        u.activo,
        u.created_at
    FROM usuarios u
    INNER JOIN roles r
        ON r.id = u.rol_id
    WHERE r.nombre = 'Recepcionista'
    ORDER BY
        u.activo DESC,
        u.nombre ASC
";

$resultRecepcionistas = $connect->query(
    $sqlRecepcionistas
);

if (!$resultRecepcionistas) {
    die('No fue posible cargar los recepcionistas.');
}

$recepcionistas = [];
$totalRecepcionistas = 0;
$totalActivos = 0;
$totalInactivos = 0;
$totalNuevosMes = 0;

while ($recepcionista = $resultRecepcionistas->fetch_assoc()) {
    $totalRecepcionistas++;

    if ((int) $recepcionista['activo'] === 1) {
        $totalActivos++;
    } else {
        $totalInactivos++;
    }

    if (
        !empty($recepcionista['created_at'])
        && date('Y-m', strtotime($recepcionista['created_at'])) === date('Y-m')
    ) {
        $totalNuevosMes++;
    }

    $recepcionistas[] = $recepcionista;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Recepcionistas | GMS</title>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="css/recepcionistas.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="receptionists-container">
    <header class="receptionists-header">
        <div>
            <p class="receptionists-eyebrow">Administración de personal</p>
            <h1>Recepcionistas</h1>
            <p>Gestiona las cuentas, accesos y credenciales del equipo de recepción.</p>
        </div>

        <button type="button" class="receptionist-add-button" data-toggle="modal" data-target="#addReceptionistModal">
            <i class="fas fa-user-plus"></i>
            <span>Agregar recepcionista</span>
        </button>
    </header>

    <section class="receptionists-stats-grid">
        <article class="receptionist-stat-card stat-total">
            <span class="receptionist-stat-icon">
                <i class="fas fa-users"></i>
            </span>

            <div>
                <small>Total registrados</small>
                <strong><?php echo $totalRecepcionistas; ?></strong>
                <p>Cuentas de recepción creadas</p>
            </div>
        </article>

        <article class="receptionist-stat-card stat-active">
            <span class="receptionist-stat-icon">
                <i class="fas fa-user-check"></i>
            </span>

            <div>
                <small>Recepcionistas activos</small>
                <strong><?php echo $totalActivos; ?></strong>
                <p>Con acceso habilitado</p>
            </div>
        </article>

        <article class="receptionist-stat-card stat-inactive">
            <span class="receptionist-stat-icon">
                <i class="fas fa-user-lock"></i>
            </span>

            <div>
                <small>Recepcionistas inactivos</small>
                <strong><?php echo $totalInactivos; ?></strong>
                <p>Sin acceso al sistema</p>
            </div>
        </article>

        <article class="receptionist-stat-card stat-new">
            <span class="receptionist-stat-icon">
                <i class="fas fa-user-clock"></i>
            </span>

            <div>
                <small>Nuevos este mes</small>
                <strong><?php echo $totalNuevosMes; ?></strong>
                <p>Registrados durante el mes actual</p>
            </div>
        </article>
    </section>

    <section class="receptionists-panel">
        <div class="receptionists-toolbar">
            <div class="receptionists-toolbar-heading">
                <h2>Directorio de recepción</h2>
                <p>Busca y administra las cuentas del personal.</p>
            </div>

            <div class="receptionists-search">
                <i class="fas fa-search"></i>

                <input type="text" id="searchReceptionist" placeholder="Buscar por nombre o teléfono" autocomplete="off">

                <button type="button" id="clearReceptionistSearch" title="Limpiar búsqueda">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="receptionists-filters">
            <div class="receptionist-filter-buttons">
                <button type="button" class="receptionist-filter-button active" data-filter="all">
                    Todos
                </button>

                <button type="button" class="receptionist-filter-button" data-filter="active">
                    Activos
                </button>

                <button type="button" class="receptionist-filter-button" data-filter="inactive">
                    Inactivos
                </button>
            </div>

            <div class="receptionists-results-count">
                <span id="visibleReceptionistsCount"><?php echo $totalRecepcionistas; ?></span>
                <small>recepcionistas mostrados</small>
            </div>
        </div>

        <div class="receptionists-crm-list" id="receptionistsList">
            <?php if ($totalRecepcionistas > 0): ?>
                <?php foreach ($recepcionistas as $recepcionista): ?>
                    <?php
                    $recepcionistaId = (int) $recepcionista['id'];
                    $activo = (int) $recepcionista['activo'];

                    $nombreSeguro = htmlspecialchars(
                        $recepcionista['nombre'],
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $telefonoSeguro = htmlspecialchars(
                        $recepcionista['telefono'],
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $fechaRegistro = !empty($recepcionista['created_at'])
                        ? date(
                            'd/m/Y',
                            strtotime($recepcionista['created_at'])
                        )
                        : 'Sin fecha';

                    $inicial = strtoupper(
                        mb_substr(
                            $recepcionista['nombre'],
                            0,
                            1,
                            'UTF-8'
                        )
                    );

                    $estadoFiltro = $activo === 1
                        ? 'active'
                        : 'inactive';
                    ?>

                    <article
                        class="receptionist-crm-card <?php echo $activo === 0 ? 'receptionist-disabled' : ''; ?>"
                        data-status="<?php echo $estadoFiltro; ?>"
                        data-search="<?php echo strtolower($nombreSeguro . ' ' . $telefonoSeguro); ?>">

                        <div class="receptionist-main-information">
                            <span class="receptionist-avatar">
                                <?php echo $inicial; ?>
                            </span>

                            <div class="receptionist-identity">
                                <div class="receptionist-name-row">
                                    <h3><?php echo $nombreSeguro; ?></h3>

                                    <?php if ($activo === 1): ?>
                                        <span class="receptionist-status-active">
                                            <i class="fas fa-circle-check"></i>
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="receptionist-status-inactive">
                                            <i class="fas fa-circle-xmark"></i>
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <span class="receptionist-role">
                                    <i class="fas fa-user-shield"></i>
                                    Recepcionista
                                </span>
                            </div>
                        </div>

                        <div class="receptionist-account-information">
                            <div class="receptionist-data-item">
                                <span>
                                    <i class="fas fa-phone"></i>
                                </span>

                                <div>
                                    <small>Teléfono de acceso</small>
                                    <strong><?php echo $telefonoSeguro; ?></strong>
                                </div>
                            </div>

                            <div class="receptionist-data-item">
                                <span>
                                    <i class="fas fa-calendar-plus"></i>
                                </span>

                                <div>
                                    <small>Fecha de registro</small>
                                    <strong><?php echo $fechaRegistro; ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="receptionist-card-actions">
                            <button
                                type="button"
                                class="receptionist-action-button action-edit editReceptionistBtn"
                                data-id="<?php echo $recepcionistaId; ?>"
                                data-nombre="<?php echo $nombreSeguro; ?>"
                                data-telefono="<?php echo $telefonoSeguro; ?>"
                                title="Editar recepcionista">

                                <i class="fas fa-pen"></i>
                                <span>Editar</span>
                            </button>

                            <button
                                type="button"
                                class="receptionist-action-button action-password resetReceptionistPasswordBtn"
                                data-id="<?php echo $recepcionistaId; ?>"
                                data-nombre="<?php echo $nombreSeguro; ?>"
                                title="Restablecer contraseña">

                                <i class="fas fa-key"></i>
                                <span>Contraseña</span>
                            </button>

                            <?php if ($activo === 1): ?>
                                <button
                                    type="button"
                                    class="receptionist-action-button action-disable toggleReceptionistBtn"
                                    data-id="<?php echo $recepcionistaId; ?>"
                                    data-estado="0"
                                    data-nombre="<?php echo $nombreSeguro; ?>"
                                    title="Desactivar recepcionista">

                                    <i class="fas fa-ban"></i>
                                    <span>Desactivar</span>
                                </button>
                            <?php else: ?>
                                <button
                                    type="button"
                                    class="receptionist-action-button action-enable toggleReceptionistBtn"
                                    data-id="<?php echo $recepcionistaId; ?>"
                                    data-estado="1"
                                    data-nombre="<?php echo $nombreSeguro; ?>"
                                    title="Activar recepcionista">

                                    <i class="fas fa-check"></i>
                                    <span>Activar</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="receptionists-empty-state <?php echo $totalRecepcionistas > 0 ? 'd-none' : ''; ?>" id="receptionistsEmptyState">
                <span>
                    <i class="fas fa-user-slash"></i>
                </span>

                <h3>No se encontraron recepcionistas</h3>
                <p>Prueba con otro nombre, teléfono o filtro de estado.</p>
            </div>
        </div>
    </section>
</main>

<!-- MODAL AGREGAR RECEPCIONISTA -->

<div class="modal fade" id="addReceptionistModal" tabindex="-1" role="dialog" aria-labelledby="addReceptionistTitle" aria-hidden="true">
    <div class="modal-dialog receptionist-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content receptionist-modal">
            <div class="modal-header receptionist-modal-header">
                <div class="receptionist-modal-heading">
                    <span class="receptionist-modal-icon modal-icon-success">
                        <i class="fas fa-user-plus"></i>
                    </span>

                    <div>
                        <h4 id="addReceptionistTitle">Agregar recepcionista</h4>
                        <p>Crea una nueva cuenta de acceso para recepción.</p>
                    </div>
                </div>

                <button type="button" class="receptionist-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createReceptionistForm">
                <div class="modal-body receptionist-modal-body">
                    <div class="receptionist-modal-notice">
                        <i class="fas fa-shield-halved"></i>
                        <p>El teléfono se utilizará para iniciar sesión en el sistema.</p>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="receptionist_nombre">Nombre completo</label>

                        <div class="receptionist-input-icon">
                            <i class="fas fa-user"></i>

                            <input
                                type="text"
                                id="receptionist_nombre"
                                class="form-control receptionist-input"
                                placeholder="Ingrese el nombre completo"
                                maxlength="100"
                                autocomplete="name"
                                required>
                        </div>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="receptionist_telefono">Teléfono</label>

                        <div class="receptionist-input-icon">
                            <i class="fas fa-phone"></i>

                            <input
                                type="tel"
                                id="receptionist_telefono"
                                class="form-control receptionist-input"
                                placeholder="10 dígitos"
                                maxlength="10"
                                inputmode="numeric"
                                autocomplete="tel"
                                required>
                        </div>
                    </div>

                    <div class="receptionist-password-section">
                        <div>
                            <h5>Contraseña inicial</h5>
                            <p>Debe contener al menos 6 caracteres.</p>
                        </div>

                        <span>
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="receptionist_password">Contraseña</label>

                        <div class="receptionist-password-input">
                            <input
                                type="password"
                                id="receptionist_password"
                                class="form-control receptionist-input"
                                placeholder="Ingrese una contraseña"
                                minlength="6"
                                maxlength="72"
                                autocomplete="new-password"
                                required>

                            <button type="button" class="receptionist-password-toggle" id="toggleReceptionistPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="receptionist_password_confirm">Confirmar contraseña</label>

                        <input
                            type="password"
                            id="receptionist_password_confirm"
                            class="form-control receptionist-input"
                            placeholder="Repita la contraseña"
                            minlength="6"
                            maxlength="72"
                            autocomplete="new-password"
                            required>
                    </div>
                </div>

                <div class="modal-footer receptionist-modal-footer">
                    <button type="button" class="receptionist-modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="receptionist-modal-button modal-button-primary" id="createReceptionistBtn">
                        <i class="fas fa-user-check"></i>
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR RECEPCIONISTA -->

<div class="modal fade" id="editReceptionistModal" tabindex="-1" role="dialog" aria-labelledby="editReceptionistTitle" aria-hidden="true">
    <div class="modal-dialog receptionist-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content receptionist-modal">
            <div class="modal-header receptionist-modal-header">
                <div class="receptionist-modal-heading">
                    <span class="receptionist-modal-icon modal-icon-primary">
                        <i class="fas fa-user-pen"></i>
                    </span>

                    <div>
                        <h4 id="editReceptionistTitle">Editar recepcionista</h4>
                        <p>Actualiza los datos y credenciales de la cuenta.</p>
                    </div>
                </div>

                <button type="button" class="receptionist-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editReceptionistForm">
                <input type="hidden" id="edit_receptionist_id">

                <div class="modal-body receptionist-modal-body">
                    <div class="form-group receptionist-form-group">
                        <label for="edit_receptionist_nombre">Nombre completo</label>

                        <div class="receptionist-input-icon">
                            <i class="fas fa-user"></i>

                            <input
                                type="text"
                                id="edit_receptionist_nombre"
                                class="form-control receptionist-input"
                                maxlength="100"
                                autocomplete="name"
                                required>
                        </div>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="edit_receptionist_telefono">Teléfono</label>

                        <div class="receptionist-input-icon">
                            <i class="fas fa-phone"></i>

                            <input
                                type="tel"
                                id="edit_receptionist_telefono"
                                class="form-control receptionist-input"
                                maxlength="10"
                                inputmode="numeric"
                                autocomplete="tel"
                                required>
                        </div>
                    </div>

                    <div class="receptionist-password-section">
                        <div>
                            <h5>Cambio de contraseña</h5>
                            <p>Déjala vacía para conservar la contraseña actual.</p>
                        </div>

                        <span>
                            <i class="fas fa-key"></i>
                        </span>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="edit_receptionist_password">Nueva contraseña</label>

                        <div class="receptionist-password-input">
                            <input
                                type="password"
                                id="edit_receptionist_password"
                                class="form-control receptionist-input"
                                placeholder="Opcional"
                                minlength="6"
                                maxlength="72"
                                autocomplete="new-password">

                            <button type="button" class="receptionist-password-toggle" id="toggleEditReceptionistPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group receptionist-form-group d-none" id="editPasswordConfirmContainer">
                        <label for="edit_receptionist_password_confirm">Confirmar nueva contraseña</label>

                        <input
                            type="password"
                            id="edit_receptionist_password_confirm"
                            class="form-control receptionist-input"
                            placeholder="Repita la nueva contraseña"
                            minlength="6"
                            maxlength="72"
                            autocomplete="new-password">
                    </div>
                </div>

                <div class="modal-footer receptionist-modal-footer">
                    <button type="button" class="receptionist-modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="receptionist-modal-button modal-button-primary" id="updateReceptionistBtn">
                        <i class="fas fa-check"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL RESTABLECER CONTRASEÑA -->

<div class="modal fade" id="resetReceptionistPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetReceptionistPasswordTitle" aria-hidden="true">
    <div class="modal-dialog receptionist-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content receptionist-modal">
            <div class="modal-header receptionist-modal-header">
                <div class="receptionist-modal-heading">
                    <span class="receptionist-modal-icon modal-icon-warning">
                        <i class="fas fa-key"></i>
                    </span>

                    <div>
                        <h4 id="resetReceptionistPasswordTitle">Restablecer contraseña</h4>
                        <p>La contraseña anterior dejará de funcionar.</p>
                    </div>
                </div>

                <button type="button" class="receptionist-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="resetReceptionistPasswordForm">
                <input type="hidden" id="reset_receptionist_id">

                <div class="modal-body receptionist-modal-body">
                    <div class="reset-password-user">
                        <span class="reset-password-avatar">
                            <i class="fas fa-user-shield"></i>
                        </span>

                        <div>
                            <small>Recepcionista seleccionado</small>
                            <strong id="resetReceptionistName"></strong>
                        </div>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="reset_receptionist_password">Nueva contraseña</label>

                        <div class="receptionist-password-input">
                            <input
                                type="password"
                                id="reset_receptionist_password"
                                class="form-control receptionist-input"
                                placeholder="Mínimo 6 caracteres"
                                minlength="6"
                                maxlength="72"
                                autocomplete="new-password"
                                required>

                            <button type="button" class="receptionist-password-toggle" id="toggleResetReceptionistPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group receptionist-form-group">
                        <label for="reset_receptionist_password_confirm">Confirmar nueva contraseña</label>

                        <input
                            type="password"
                            id="reset_receptionist_password_confirm"
                            class="form-control receptionist-input"
                            placeholder="Repita la nueva contraseña"
                            minlength="6"
                            maxlength="72"
                            autocomplete="new-password"
                            required>
                    </div>

                    <div class="receptionist-security-warning">
                        <i class="fas fa-triangle-exclamation"></i>
                        <p>La persona deberá utilizar la nueva contraseña en su próximo inicio de sesión.</p>
                    </div>
                </div>

                <div class="modal-footer receptionist-modal-footer">
                    <button type="button" class="receptionist-modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="receptionist-modal-button modal-button-warning" id="resetReceptionistPasswordSubmitBtn">
                        <i class="fas fa-key"></i>
                        Restablecer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="js/recepcionistas.js"></script>
</body>
</html>

<?php

$connect->close();

?>
