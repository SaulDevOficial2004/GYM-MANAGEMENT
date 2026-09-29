<?php

require_once __DIR__ . '/includes/auth.php';

requireRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

require_once __DIR__ . '/php_action/conn_db.php';

use GMS\Repository\MembresiaRepository;

$telefono = $_SESSION['telefono'];
$nombre = $_SESSION['nombre'];

$membresiaRepo = new MembresiaRepository($connect);

$membresias = [];
$totalMembresias = 0;
$totalActivas = 0;
$totalInactivas = 0;
$totalPromociones = 0;

foreach ($membresiaRepo->listar($membresiaRepo->contar(), 0) as $membresia) {
    $totalMembresias++;

    if ((int) $membresia['activo'] === 1) {
        $totalActivas++;
    } else {
        $totalInactivas++;
    }

    if (
        (int) $membresia['promocion'] === 1
        && !empty($membresia['precio_promocion'])
    ) {
        $totalPromociones++;
    }

    $membresias[] = $membresia;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Membresías | GMS</title>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="css/membresias.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="memberships-container">
    <header class="memberships-header">
        <div>
            <p class="memberships-eyebrow">Configuración comercial</p>
            <h1>Membresías</h1>
            <p>Administra los planes, precios, promociones y periodos disponibles.</p>
        </div>

        <button
            type="button"
            class="membership-add-button"
            data-toggle="modal"
            data-target="#addMembershipModal">

            <i class="fas fa-plus"></i>
            <span>Nueva membresía</span>
        </button>
    </header>

    <section class="memberships-stats-grid">
        <article class="membership-stat-card stat-total">
            <span class="membership-stat-icon">
                <i class="fas fa-id-card"></i>
            </span>

            <div>
                <small>Total de planes</small>
                <strong><?php echo $totalMembresias; ?></strong>
                <p>Membresías registradas</p>
            </div>
        </article>

        <article class="membership-stat-card stat-active">
            <span class="membership-stat-icon">
                <i class="fas fa-check-circle"></i>
            </span>

            <div>
                <small>Membresías activas</small>
                <strong><?php echo $totalActivas; ?></strong>
                <p>Disponibles para asignar</p>
            </div>
        </article>

        <article class="membership-stat-card stat-promotion">
            <span class="membership-stat-icon">
                <i class="fas fa-tags"></i>
            </span>

            <div>
                <small>En promoción</small>
                <strong><?php echo $totalPromociones; ?></strong>
                <p>Planes con precio especial</p>
            </div>
        </article>

        <article class="membership-stat-card stat-inactive">
            <span class="membership-stat-icon">
                <i class="fas fa-ban"></i>
            </span>

            <div>
                <small>Membresías inactivas</small>
                <strong><?php echo $totalInactivas; ?></strong>
                <p>No disponibles actualmente</p>
            </div>
        </article>
    </section>

    <section class="memberships-panel">
        <div class="memberships-toolbar">
            <div class="memberships-toolbar-heading">
                <h2>Catálogo de membresías</h2>
                <p>Busca, filtra y administra los planes del gimnasio.</p>
            </div>

            <div class="memberships-search">
                <i class="fas fa-search"></i>

                <input
                    type="text"
                    id="searchMembership"
                    placeholder="Buscar por nombre o descripción"
                    autocomplete="off">

                <button
                    type="button"
                    id="clearMembershipSearch"
                    title="Limpiar búsqueda">

                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="memberships-filters">
            <div class="membership-filter-buttons">
                <button
                    type="button"
                    class="membership-filter-button active"
                    data-filter="all">

                    Todas
                </button>

                <button
                    type="button"
                    class="membership-filter-button"
                    data-filter="active">

                    Activas
                </button>

                <button
                    type="button"
                    class="membership-filter-button"
                    data-filter="promotion">

                    Promociones
                </button>

                <button
                    type="button"
                    class="membership-filter-button"
                    data-filter="inactive">

                    Inactivas
                </button>
            </div>

            <div class="memberships-results-count">
                <span id="visibleMembershipsCount">
                    <?php echo $totalMembresias; ?>
                </span>

                <small>membresías mostradas</small>
            </div>
        </div>

        <div class="memberships-grid" id="membershipsList">
            <?php if ($totalMembresias > 0): ?>
                <?php foreach ($membresias as $membresia): ?>
                    <?php
                    $membresiaId = (int) $membresia['id'];
                    $activa = (int) $membresia['activo'] === 1;
                    $tienePromocion =
                        (int) $membresia['promocion'] === 1
                        && !empty($membresia['precio_promocion']);

                    $nombreSeguro = htmlspecialchars(
                        $membresia['nombre'],
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $descripcionSegura = htmlspecialchars(
                        $membresia['descripcion'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $precioNormal = (float) $membresia['precio'];
                    $precioPromocion = (float) (
                        $membresia['precio_promocion'] ?? 0
                    );

                    $dias = (int) $membresia['dias'];

                    $estadoFiltro = $activa
                        ? 'active'
                        : 'inactive';

                    $promocionFiltro = $tienePromocion
                        ? 'promotion'
                        : 'regular';
                    ?>

                    <article
                        class="membership-card <?php echo !$activa ? 'membership-disabled' : ''; ?>"
                        data-status="<?php echo $estadoFiltro; ?>"
                        data-promotion="<?php echo $promocionFiltro; ?>"
                        data-search="<?php echo strtolower(
                            $nombreSeguro . ' ' . $descripcionSegura
                        ); ?>">

                        <div class="membership-card-header">
                            <div class="membership-title-area">
                                <span class="membership-card-icon">
                                    <i class="fas fa-id-card"></i>
                                </span>

                                <div>
                                    <h3><?php echo $nombreSeguro; ?></h3>

                                    <span class="membership-duration">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo $dias; ?>
                                        <?php echo $dias === 1 ? 'día' : 'días'; ?>
                                    </span>
                                </div>
                            </div>

                            <?php if ($activa): ?>
                                <span class="membership-status-active">
                                    <i class="fas fa-check-circle"></i>
                                    Activa
                                </span>
                            <?php else: ?>
                                <span class="membership-status-inactive">
                                    <i class="fas fa-times-circle"></i>
                                    Inactiva
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="membership-description">
                            <?php if ($descripcionSegura !== ''): ?>
                                <p><?php echo $descripcionSegura; ?></p>
                            <?php else: ?>
                                <p class="membership-description-empty">
                                    Sin descripción registrada.
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="membership-price-section">
                            <?php if ($tienePromocion): ?>
                                <div class="membership-promotion-badge">
                                    <i class="fas fa-tag"></i>
                                    Promoción activa
                                </div>

                                <div class="membership-prices">
                                    <span class="membership-old-price">
                                        $<?php echo number_format(
                                            $precioNormal,
                                            2
                                        ); ?>
                                    </span>

                                    <strong>
                                        $<?php echo number_format(
                                            $precioPromocion,
                                            2
                                        ); ?>
                                    </strong>
                                </div>
                            <?php else: ?>
                                <div class="membership-regular-label">
                                    Precio normal
                                </div>

                                <div class="membership-prices">
                                    <strong>
                                        $<?php echo number_format(
                                            $precioNormal,
                                            2
                                        ); ?>
                                    </strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="membership-card-footer">
                            <button
                                type="button"
                                class="membership-action-button action-edit editMembershipBtn"
                                data-id="<?php echo $membresiaId; ?>"
                                data-nombre="<?php echo $nombreSeguro; ?>"
                                data-descripcion="<?php echo $descripcionSegura; ?>"
                                data-precio="<?php echo $precioNormal; ?>"
                                data-promocion="<?php echo $tienePromocion ? 1 : 0; ?>"
                                data-precio_promocion="<?php echo $precioPromocion; ?>"
                                data-dias="<?php echo $dias; ?>">

                                <i class="fas fa-pen"></i>
                                <span>Editar</span>
                            </button>

                            <?php if ($activa): ?>
                                <button
                                    type="button"
                                    class="membership-action-button action-disable toggleMembershipBtn"
                                    data-id="<?php echo $membresiaId; ?>"
                                    data-estado="1"
                                    data-nombre="<?php echo $nombreSeguro; ?>">

                                    <i class="fas fa-ban"></i>
                                    <span>Desactivar</span>
                                </button>
                            <?php else: ?>
                                <button
                                    type="button"
                                    class="membership-action-button action-enable toggleMembershipBtn"
                                    data-id="<?php echo $membresiaId; ?>"
                                    data-estado="0"
                                    data-nombre="<?php echo $nombreSeguro; ?>">

                                    <i class="fas fa-check"></i>
                                    <span>Activar</span>
                                </button>
                            <?php endif; ?>

                            <button
                                type="button"
                                class="membership-action-button action-delete deleteMembershipBtn"
                                data-id="<?php echo $membresiaId; ?>"
                                data-nombre="<?php echo $nombreSeguro; ?>">

                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <div
                class="memberships-empty-state <?php echo $totalMembresias > 0 ? 'd-none' : ''; ?>"
                id="membershipsEmptyState">

                <span>
                    <i class="fas fa-id-card"></i>
                </span>

                <h3>No se encontraron membresías</h3>
                <p>Prueba con otro término o cambia el filtro seleccionado.</p>
            </div>
        </div>
    </section>
</main>

<!-- MODAL CREAR MEMBRESÍA -->

<div
    class="modal fade"
    id="addMembershipModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="addMembershipTitle"
    aria-hidden="true">

    <div
        class="modal-dialog membership-modal-dialog modal-dialog-centered"
        role="document">

        <div class="modal-content membership-modal">
            <div class="modal-header membership-modal-header">
                <div class="membership-modal-heading">
                    <span class="membership-modal-icon modal-icon-success">
                        <i class="fas fa-plus"></i>
                    </span>

                    <div>
                        <h4 id="addMembershipTitle">
                            Nueva membresía
                        </h4>

                        <p>
                            Configura un nuevo plan para los clientes.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="membership-modal-close"
                    data-dismiss="modal"
                    aria-label="Cerrar">

                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createMembershipForm">
                <div class="modal-body membership-modal-body">
                    <div class="membership-form-grid">
                        <div class="form-group membership-form-group">
                            <label for="nombre">
                                Nombre
                            </label>

                            <div class="membership-input-icon">
                                <i class="fas fa-id-card"></i>

                                <input
                                    type="text"
                                    id="nombre"
                                    class="form-control membership-input"
                                    placeholder="Ej. Mensualidad"
                                    required>
                            </div>
                        </div>

                        <div class="form-group membership-form-group">
                            <label for="dias">
                                Duración en días
                            </label>

                            <div class="membership-input-icon">
                                <i class="fas fa-calendar-alt"></i>

                                <input
                                    type="number"
                                    id="dias"
                                    class="form-control membership-input"
                                    placeholder="Ej. 30"
                                    min="1"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group membership-form-group">
                        <label for="descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            rows="3"
                            class="form-control membership-input membership-textarea"
                            placeholder="Describe las características del plan"></textarea>
                    </div>

                    <div class="membership-price-heading">
                        <div>
                            <h5>Configuración de precio</h5>
                            <p>Define el costo normal y una promoción opcional.</p>
                        </div>

                        <span>
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                    </div>

                    <div class="form-group membership-form-group">
                        <label for="precio">
                            Precio normal
                        </label>

                        <div class="membership-currency-input">
                            <span>$</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="precio"
                                class="form-control membership-input"
                                placeholder="0.00"
                                required>
                        </div>
                    </div>

                    <div class="membership-promotion-switch">
                        <div>
                            <strong>Activar promoción</strong>
                            <small>Permite asignar un precio especial.</small>
                        </div>

                        <label class="membership-switch">
                            <input
                                type="checkbox"
                                id="promocion">

                            <span></span>
                        </label>
                    </div>

                    <div
                        id="promoContainer"
                        class="form-group membership-form-group d-none">

                        <label for="precio_promocion">
                            Precio de promoción
                        </label>

                        <div class="membership-currency-input promotion-price-input">
                            <span>$</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="precio_promocion"
                                class="form-control membership-input"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="modal-footer membership-modal-footer">
                    <button
                        type="reset"
                        class="membership-modal-button modal-button-secondary">

                        <i class="fas fa-undo"></i>
                        Limpiar
                    </button>

                    <button
                        type="submit"
                        class="membership-modal-button modal-button-primary"
                        id="createMembershipBtn">

                        <i class="fas fa-check"></i>
                        Guardar membresía
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR MEMBRESÍA -->

<div
    class="modal fade"
    id="editMembershipModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="editMembershipTitle"
    aria-hidden="true">

    <div
        class="modal-dialog membership-modal-dialog modal-dialog-centered"
        role="document">

        <div class="modal-content membership-modal">
            <div class="modal-header membership-modal-header">
                <div class="membership-modal-heading">
                    <span class="membership-modal-icon modal-icon-primary">
                        <i class="fas fa-pen"></i>
                    </span>

                    <div>
                        <h4 id="editMembershipTitle">
                            Editar membresía
                        </h4>

                        <p>
                            Actualiza la información y los precios del plan.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="membership-modal-close"
                    data-dismiss="modal"
                    aria-label="Cerrar">

                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editMembershipForm">
                <input
                    type="hidden"
                    id="edit_id">

                <div class="modal-body membership-modal-body">
                    <div class="membership-form-grid">
                        <div class="form-group membership-form-group">
                            <label for="edit_nombre">
                                Nombre
                            </label>

                            <div class="membership-input-icon">
                                <i class="fas fa-id-card"></i>

                                <input
                                    type="text"
                                    id="edit_nombre"
                                    class="form-control membership-input"
                                    required>
                            </div>
                        </div>

                        <div class="form-group membership-form-group">
                            <label for="edit_dias">
                                Duración en días
                            </label>

                            <div class="membership-input-icon">
                                <i class="fas fa-calendar-alt"></i>

                                <input
                                    type="number"
                                    id="edit_dias"
                                    class="form-control membership-input"
                                    min="1"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group membership-form-group">
                        <label for="edit_descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="edit_descripcion"
                            rows="3"
                            class="form-control membership-input membership-textarea"></textarea>
                    </div>

                    <div class="membership-price-heading">
                        <div>
                            <h5>Configuración de precio</h5>
                            <p>Modifica el costo normal o el precio promocional.</p>
                        </div>

                        <span>
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                    </div>

                    <div class="form-group membership-form-group">
                        <label for="edit_precio">
                            Precio normal
                        </label>

                        <div class="membership-currency-input">
                            <span>$</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="edit_precio"
                                class="form-control membership-input"
                                required>
                        </div>
                    </div>

                    <div class="membership-promotion-switch">
                        <div>
                            <strong>Activar promoción</strong>
                            <small>Utiliza un precio especial para este plan.</small>
                        </div>

                        <label class="membership-switch">
                            <input
                                type="checkbox"
                                id="edit_promocion">

                            <span></span>
                        </label>
                    </div>

                    <div
                        id="editPromoContainer"
                        class="form-group membership-form-group d-none">

                        <label for="edit_precio_promocion">
                            Precio de promoción
                        </label>

                        <div class="membership-currency-input promotion-price-input">
                            <span>$</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="edit_precio_promocion"
                                class="form-control membership-input">
                        </div>
                    </div>
                </div>

                <div class="modal-footer membership-modal-footer">
                    <button
                        type="button"
                        class="membership-modal-button modal-button-secondary"
                        data-dismiss="modal">

                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="membership-modal-button modal-button-primary"
                        id="updateMembershipAdminBtn">

                        <i class="fas fa-check"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/modals/add_person_modal.php'; ?>
<?php include 'includes/footer.php'; ?>

<script src="js/membresias.js"></script>
<script src="js/personas/add_person.js"></script>
</body>
</html>

<?php

$connect->close();

?>
