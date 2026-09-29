<?php

require_once __DIR__ . '/includes/auth.php';

requireRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

require_once __DIR__ . '/php_action/conn_db.php';

use GMS\Repository\PersonaRepository;

$telefono = $_SESSION['telefono'];
$nombre = $_SESSION['nombre'];

$personaRepo = new PersonaRepository($connect);

$paginaActual = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 25;

$statsPersonas = $personaRepo->estadisticas();

$totalPersonas = $statsPersonas['total'];
$totalActivas = $statsPersonas['activas'];
$totalInhabilitadas = $statsPersonas['inhabilitadas'];
$totalVencidas = $statsPersonas['vencidas'];
$totalPorVencer = $statsPersonas['por_vencer'];

$totalPaginas = max(1, (int) ceil($totalPersonas / $porPagina));

if ($paginaActual > $totalPaginas) {
    $paginaActual = $totalPaginas;
}

$fechaActual = new DateTime();
$fechaLimite = (clone $fechaActual)->modify('+7 days');

$personas = $personaRepo->listar($porPagina, ($paginaActual - 1) * $porPagina);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Personas | GMS</title>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="css/personas.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<main class="persons-container">
    <header class="persons-header">
        <div>
            <p class="persons-eyebrow">Gestión de clientes</p>
            <h1>Personas registradas</h1>
            <p>Administra clientes, membresías y accesos dentro del gimnasio.</p>
        </div>
        <button type="button" class="persons-add-button" data-toggle="modal" data-target="#addPersonModal">
            <i class="fas fa-user-plus"></i>
            <span>Agregar persona</span>
        </button>
    </header>

    <section class="persons-stats-grid">
        <article class="person-stat-card stat-total">
            <span class="person-stat-icon">
                <i class="fas fa-users"></i>
            </span>
            <div>
                <small>Total registradas</small>
                <strong><?php echo $totalPersonas; ?></strong>
                <p>Personas dentro del sistema</p>
            </div>
        </article>
        <article class="person-stat-card stat-active">
            <span class="person-stat-icon">
                <i class="fas fa-user-check"></i>
            </span>
            <div>
                <small>Personas activas</small>
                <strong><?php echo $totalActivas; ?></strong>
                <p>Habilitadas actualmente</p>
            </div>
        </article>
        <article class="person-stat-card stat-warning">
            <span class="person-stat-icon">
                <i class="fas fa-clock"></i>
            </span>
            <div>
                <small>Por vencer</small>
                <strong><?php echo $totalPorVencer; ?></strong>
                <p>Vencen durante los próximos 7 días</p>
            </div>
        </article>
        <article class="person-stat-card stat-expired">
            <span class="person-stat-icon">
                <i class="fas fa-calendar-xmark"></i>
            </span>
            <div>
                <small>Membresías vencidas</small>
                <strong><?php echo $totalVencidas; ?></strong>
                <p>Requieren renovación</p>
            </div>
        </article>
    </section>

    <section class="persons-panel">
        <div class="persons-toolbar">
            <div class="persons-toolbar-heading">
                <h2>Directorio de personas</h2>
                <p>Busca y administra los registros del gimnasio.</p>
            </div>
            <div class="persons-toolbar-actions">
                <button type="button" class="persons-inactive-button" id="toggleInactivePersons">
                    <i class="fas fa-eye"></i>
                    <span>Mostrar inhabilitados</span>
                    <small><?php echo $totalInhabilitadas; ?></small>
                </button>
            </div>
        </div>

        <div class="persons-filters">
            <div class="persons-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchPerson" placeholder="Buscar por nombre o folio" autocomplete="off">
            </div>
            <div class="persons-filter-buttons">
                <button type="button" class="person-filter-button active" data-filter="all">Todas</button>
                <button type="button" class="person-filter-button" data-filter="active">Activas</button>
                <button type="button" class="person-filter-button" data-filter="expiring">Por vencer</button>
                <button type="button" class="person-filter-button" data-filter="expired">Vencidas</button>
            </div>
        </div>

        <div class="persons-results-header">
            <div>
                <span id="visiblePersonsCount"><?php echo $totalActivas; ?></span>
                <small>personas mostradas</small>
            </div>
            <span class="persons-view-label">
                <i class="fas fa-address-book"></i>
                Vista CRM
            </span>
        </div>

        <div class="persons-crm-list" id="personsList">
            <?php if ($totalPersonas > 0): ?>
                <?php foreach ($personas as $persona): ?>
                    <?php
                    $personaId = (int) $persona['id'];
                    $estatusSistema = (int) $persona['estatus'];

                    $fechaInicio = date(
                        'd/m/Y',
                        strtotime(
                            $persona['fecha_ini']
                        )
                    );

                    $fechaFin = date(
                        'd/m/Y',
                        strtotime(
                            $persona['fecha_fin']
                        )
                    );

                    $fechaFinObj = new DateTime(
                        $persona['fecha_fin']
                    );

                    $membresiaEstado = 'active';
                    $membresiaTexto = 'Activa';
                    $membresiaClase = 'membership-active';

                    if ($fechaFinObj < $fechaActual) {
                        $membresiaEstado = 'expired';
                        $membresiaTexto = 'Vencida';
                        $membresiaClase = 'membership-expired';
                    } elseif ($fechaFinObj <= $fechaLimite) {
                        $membresiaEstado = 'expiring';
                        $membresiaTexto = 'Por vencer';
                        $membresiaClase = 'membership-expiring';
                    }

                    $nombreSeguro = htmlspecialchars(
                        $persona['nombre'],
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $folioSeguro = htmlspecialchars(
                        $persona['folio'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                    <article class="person-crm-card <?php echo $estatusSistema === 2 ? 'person-disabled' : ''; ?>"
                        data-system-status="<?php echo $estatusSistema; ?>"
                        data-membership-status="<?php echo $membresiaEstado; ?>"
                        data-search="<?php echo strtolower($nombreSeguro . ' ' . $folioSeguro); ?>">
                        <div class="person-main-information">
                            <span class="person-avatar">
                                <?php echo strtoupper(
                                    mb_substr(
                                        $persona['nombre'],
                                        0,
                                        1,
                                        'UTF-8'
                                    )
                                ); ?>
                            </span>
                            <div class="person-identity">
                                <div class="person-name-row">
                                    <h3><?php echo $nombreSeguro; ?></h3>
                                    <?php if ($estatusSistema === 1): ?>
                                        <span class="system-status-enabled">
                                            <i class="fas fa-circle-check"></i>
                                            Habilitada
                                        </span>
                                    <?php else: ?>
                                        <span class="system-status-disabled">
                                            <i class="fas fa-circle-xmark"></i>
                                            Inhabilitada
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="person-folio">
                                    <small>Folio</small>
                                    <strong id="folio-<?php echo $personaId; ?>" class="folio-hidden">**********</strong>
                                    <button type="button" class="toggleFolioBtn"
                                        data-id="<?php echo $personaId; ?>"
                                        data-folio="<?php echo $folioSeguro; ?>"
                                        title="Mostrar u ocultar folio">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="person-membership-information">
                            <div class="person-data-item">
                                <span class="person-data-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </span>
                                <div>
                                    <small>Fecha de inicio</small>
                                    <strong><?php echo $fechaInicio; ?></strong>
                                </div>
                            </div>
                            <div class="person-data-item">
                                <span class="person-data-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                                <div>
                                    <small>Fecha de vencimiento</small>
                                    <strong><?php echo $fechaFin; ?></strong>
                                </div>
                            </div>
                            <div class="person-membership-status">
                                <small>Estado de membresía</small>
                                <span class="<?php echo $membresiaClase; ?>">
                                    <?php echo $membresiaTexto; ?>
                                </span>
                            </div>
                        </div>

                        <div class="person-card-actions">
                            <button type="button" class="person-primary-action editBtn"
                                data-id="<?php echo $personaId; ?>"
                                data-nombre="<?php echo $nombreSeguro; ?>"
                                data-fecha_ini="<?php echo htmlspecialchars(
                                    $persona['fecha_ini'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                data-fecha_fin="<?php echo htmlspecialchars(
                                    $persona['fecha_fin'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>">
                                <i class="fas fa-pen"></i>
                                <span>Editar</span>
                            </button>

                            <button type="button" class="person-primary-action copiarEnlaceBtn"
                                data-folio="<?php echo $folioSeguro; ?>"
                                title="Copiar enlace del portal">
                                <i class="fas fa-link"></i>
                                <span>Enlace portal</span>
                            </button>

                            <?php if ($estatusSistema === 1): ?>
                                <button type="button" class="person-danger-action disablePersonBtn"
                                    data-id="<?php echo $personaId; ?>"
                                    data-nombre="<?php echo $nombreSeguro; ?>">
                                    <i class="fas fa-ban"></i>
                                    <span>Inhabilitar</span>
                                </button>
                            <?php else: ?>
                                <button type="button" class="person-success-action enablePersonBtn"
                                    data-id="<?php echo $personaId; ?>"
                                    data-nombre="<?php echo $nombreSeguro; ?>">
                                    <i class="fas fa-check"></i>
                                    <span>Habilitar</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="persons-empty-state <?php echo $totalPersonas > 0 ? 'd-none' : ''; ?>" id="personsEmptyState">
                <span>
                    <i class="fas fa-user-slash"></i>
                </span>
                <h3>No se encontraron personas</h3>
                <p>Prueba con otro término de búsqueda o cambia los filtros seleccionados.</p>
            </div>
        </div>
        <?php include __DIR__ . '/includes/partials/paginacion.php'; ?>
    </section>
</main>

<!-- MODAL EDITAR PERSONA -->
<div class="modal fade" id="editPersonModal" tabindex="-1" role="dialog" aria-labelledby="editPersonTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content persons-modal">
            <div class="modal-header persons-modal-header">
                <div class="persons-modal-heading">
                    <span class="persons-modal-icon">
                        <i class="fas fa-user-pen"></i>
                    </span>
                    <div>
                        <h4 id="editPersonTitle">Editar persona</h4>
                        <p>Actualiza la información y las fechas de membresía.</p>
                    </div>
                </div>
                <button type="button" class="persons-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editPersonForm">
                <input type="hidden" id="edit_id">
                <div class="modal-body persons-modal-body">
                    <div class="form-group persons-form-group">
                        <label for="edit_nombre">Nombre completo</label>
                        <input type="text" id="edit_nombre" class="form-control persons-input" required>
                    </div>
                    <div class="persons-form-grid">
                        <div class="form-group persons-form-group">
                            <label for="edit_fecha_ini">Fecha de inicio</label>
                            <input type="date" id="edit_fecha_ini" class="form-control persons-input" required>
                        </div>
                        <div class="form-group persons-form-group">
                            <label for="edit_fecha_fin">Fecha de vencimiento</label>
                            <input type="date" id="edit_fecha_fin" class="form-control persons-input" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer persons-modal-footer">
                    <button type="button" class="persons-modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="persons-modal-button modal-button-primary">
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

<script src="js/personas.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/alerts.js"></script>
<script src="js/personas/add_person.js"></script>
</body>
</html>
<?php

$connect->close();

?>
