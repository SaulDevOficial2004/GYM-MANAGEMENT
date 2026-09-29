<?php

require_once __DIR__ . '/includes/auth.php';

requireRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

require_once __DIR__ . '/php_action/conn_db.php';

use GMS\Repository\VisitanteRepository;

$telefono = $_SESSION['telefono'];
$nombre = $_SESSION['nombre'];

$visitanteRepo = new VisitanteRepository($connect);

/* RESUMEN DE VISITAS */

$resumen = $visitanteRepo->resumen();

$totalVisitas = (int) ($resumen['total_visitas'] ?? 0);
$visitantesUnicos = (int) ($resumen['visitantes_unicos'] ?? 0);
$visitasHoy = (int) ($resumen['visitas_hoy'] ?? 0);
$visitasMes = (int) ($resumen['visitas_mes'] ?? 0);

/* HISTORIAL */

$paginaActual = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 25;

$totalPaginas = max(1, (int) ceil($visitanteRepo->contar() / $porPagina));

if ($paginaActual > $totalPaginas) {
    $paginaActual = $totalPaginas;
}

$visitasAgrupadas = [];

foreach ($visitanteRepo->listarHistorial($porPagina, ($paginaActual - 1) * $porPagina) as $visita) {
        $fechaGrupo = date(
            'Y-m-d',
            strtotime(
                $visita['fecha_visita']
            )
        );

        if (!isset($visitasAgrupadas[$fechaGrupo])) {
            $visitasAgrupadas[$fechaGrupo] = [];
        }

        $visitasAgrupadas[$fechaGrupo][] = $visita;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Visitantes | GMS</title>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="css/visitantes.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="visitors-container">
    <header class="visitors-header">
        <div>
            <p class="visitors-eyebrow">Control de accesos</p>
            <h1>Historial de visitantes</h1>
            <p>Consulta los registros y registra nuevas visitas rápidamente.</p>
        </div>
        <a href="pagina.php" class="visitors-back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Volver al Dashboard</span>
        </a>
    </header>

    <section class="visitors-stats-grid">
        <article class="visitor-stat-card stat-today">
            <span class="visitor-stat-icon">
                <i class="fas fa-calendar-day"></i>
            </span>
            <div>
                <small>Visitas hoy</small>
                <strong><?php echo $visitasHoy; ?></strong>
                <p>Entradas registradas durante el día</p>
            </div>
        </article>

        <article class="visitor-stat-card stat-month">
            <span class="visitor-stat-icon">
                <i class="fas fa-calendar-days"></i>
            </span>
            <div>
                <small>Visitas del mes</small>
                <strong><?php echo $visitasMes; ?></strong>
                <p>Entradas del mes actual</p>
            </div>
        </article>

        <article class="visitor-stat-card stat-unique">
            <span class="visitor-stat-icon">
                <i class="fas fa-users"></i>
            </span>
            <div>
                <small>Visitantes únicos</small>
                <strong><?php echo $visitantesUnicos; ?></strong>
                <p>Personas registradas en el historial</p>
            </div>
        </article>

        <article class="visitor-stat-card stat-total">
            <span class="visitor-stat-icon">
                <i class="fas fa-clock-rotate-left"></i>
            </span>
            <div>
                <small>Visitas históricas</small>
                <strong><?php echo $totalVisitas; ?></strong>
                <p>Total de entradas registradas</p>
            </div>
        </article>
    </section>

    <section class="visitors-panel">
        <div class="visitors-toolbar">
            <div class="visitors-toolbar-heading">
                <h2>Registro de visitas</h2>
                <p>Las entradas están agrupadas por fecha.</p>
            </div>

            <div class="visitors-search">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="searchVisitantes"
                    placeholder="Buscar visitante por nombre"
                    autocomplete="off">
                <button type="button" id="clearVisitorSearch" title="Limpiar búsqueda">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="visitors-results-summary">
            <div>
                <span id="visibleVisitsCount">
                    <?php echo $totalVisitas; ?>
                </span>
                <small>visitas mostradas</small>
            </div>

            <span class="visitors-view-label">
                <i class="fas fa-layer-group"></i>
                Agrupadas por fecha
            </span>
        </div>

        <div class="visits-timeline" id="visitasContainer">
            <?php if (!empty($visitasAgrupadas)): ?>
                <?php foreach ($visitasAgrupadas as $fechaGrupo => $visitas): ?>
                    <?php
                    $fechaObjeto = new DateTime(
                        $fechaGrupo
                    );

                    $fechaTitulo = $fechaObjeto->format(
                        'd/m/Y'
                    );

                    $esHoy = $fechaGrupo === date(
                        'Y-m-d'
                    );

                    $esAyer = $fechaGrupo === date(
                        'Y-m-d',
                        strtotime('-1 day')
                    );

                    $diasSemana = [
                        1 => 'Lunes',
                        2 => 'Martes',
                        3 => 'Miércoles',
                        4 => 'Jueves',
                        5 => 'Viernes',
                        6 => 'Sábado',
                        7 => 'Domingo'
                    ];

                    if ($esHoy) {
                        $fechaDescripcion = 'Hoy';
                    } elseif ($esAyer) {
                        $fechaDescripcion = 'Ayer';
                    } else {
                        $numeroDia = (int) $fechaObjeto->format('N');
                        $fechaDescripcion = $diasSemana[$numeroDia];
}
                    ?>

                    <section class="visit-date-group" data-date="<?php echo $fechaGrupo; ?>">
                        <div class="visit-date-header">
                            <div class="visit-date-marker">
                                <span></span>
                            </div>

                            <div class="visit-date-information">
                                <div>
                                    <h3><?php echo $fechaDescripcion; ?></h3>
                                    <small><?php echo $fechaTitulo; ?></small>
                                </div>

                                <span class="visit-date-counter">
                                    <?php echo count($visitas); ?>
                                    <?php echo count($visitas) === 1 ? 'visita' : 'visitas'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="visit-list">
                            <?php foreach ($visitas as $visita): ?>
                                <?php
                                $visitanteId = (int) $visita['id'];

                                $nombreVisitante = htmlspecialchars(
                                    $visita['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                $fechaCompleta = date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $visita['fecha_visita']
                                    )
                                );

                                $horaVisita = date(
                                    'H:i',
                                    strtotime(
                                        $visita['fecha_visita']
                                    )
                                );

                                $inicial = strtoupper(
                                    mb_substr(
                                        $visita['nombre'],
                                        0,
                                        1,
                                        'UTF-8'
                                    )
                                );
                                ?>

                                <article
                                    class="visitor-crm-card"
                                    data-search="<?php echo strtolower($nombreVisitante); ?>">

                                    <div class="visitor-main-information">
                                        <span class="visitor-avatar">
                                            <?php echo $inicial; ?>
                                        </span>

                                        <div class="visitor-identity">
                                            <h4><?php echo $nombreVisitante; ?></h4>
                                            <span>
                                                <i class="fas fa-user-check"></i>
                                                Visitante registrado
                                            </span>
                                        </div>
                                    </div>

                                    <div class="visitor-visit-information">
                                        <div class="visitor-data-item">
                                            <span>
                                                <i class="fas fa-clock"></i>
                                            </span>

                                            <div>
                                                <small>Hora de entrada</small>
                                                <strong><?php echo $horaVisita; ?></strong>
                                            </div>
                                        </div>

                                        <div class="visitor-data-item">
                                            <span>
                                                <i class="fas fa-calendar-check"></i>
                                            </span>

                                            <div>
                                                <small>Registro completo</small>
                                                <strong><?php echo $fechaCompleta; ?></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="visitor-register-button btn-register-visit"
                                        data-id="<?php echo $visitanteId; ?>"
                                        data-nombre="<?php echo $nombreVisitante; ?>">

                                        <i class="fas fa-plus"></i>
                                        <span>Registrar visita</span>
                                    </button>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>

            <div
                class="visitors-empty-state <?php echo $totalVisitas > 0 ? 'd-none' : ''; ?>"
                id="visitorsEmptyState">

                <span>
                    <i class="fas fa-user-clock"></i>
                </span>

                <h3>No se encontraron visitas</h3>
                <p>No existen registros que coincidan con la búsqueda.</p>
            </div>
        </div>
        <?php include __DIR__ . '/includes/partials/paginacion.php'; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/visitantes.js"></script>
</body>
</html>

<?php

$connect->close();

?>
