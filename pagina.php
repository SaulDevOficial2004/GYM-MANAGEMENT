<?php
//SESION
    require_once __DIR__ . '/includes/auth.php';

    requireLogin();

    $telefono = $_SESSION['telefono'];
    $nombre = $_SESSION['nombre'];
    $rolNombre = $_SESSION['rol_nombre'] ?? 'Usuario';

    require_once 'php_action/conn_db.php';

    use GMS\Repository\ComprobanteRepository;
    use GMS\Repository\MembresiaRepository;
    use GMS\Repository\PersonaRepository;
    use GMS\Repository\TransferenciaRepository;
    use GMS\Repository\VentaRepository;

    $comprobanteRepo = new ComprobanteRepository($connect);
    $membresiaRepo = new MembresiaRepository($connect);
    $personaRepo = new PersonaRepository($connect);
    $transferenciaRepo = new TransferenciaRepository($connect);
    $ventaRepo = new VentaRepository($connect);

    //COMPROBANTES PENDIENTES

        $comprobantesPendientes = $comprobanteRepo->listarPorEstatus('PENDIENTE');
        $totalPendientes = count($comprobantesPendientes);

        /*=====================================
        COLOR DE LA CARD
        =====================================*/

        $cardColor = 'green';

        if($totalPendientes >= 6){

            $cardColor = 'red';

        }else if($totalPendientes >= 1){

            $cardColor = 'yellow';

        }

    $membresiasActivas = $membresiaRepo->listarActivas();

//VISITAS DE HOY
$total_visitas_hoy = $ventaRepo->visitasHoy();

//VENTAS DE HOY
$total_ventas_hoy = $ventaRepo->ventasHoyTotal();

//CONFIGURACIÓN TRANSFERENCIAS

$configuracionTransferencias = $transferenciaRepo->obtenerConfiguracion();

?>

<!--INICIO DE HTML-->

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard | GMS</title>

    <!--Links del head-->
    <?php include 'includes/head.php' ?>

    <!-- CSS PERSONALIZADO -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body<?php $flashes=[]; if(isset($_GET['success'])){ $flashes[]='loginSuccess'; } if(isset($_GET['update'])){ $flashes[]='updateMembership'; } if($flashes!==[]){ echo ' data-flash="'.implode(',',$flashes).'"'; } ?>>

<!--Navbar general-->
<?php include 'includes/navbar.php' ?>


<!-- CONTENIDO -->
<div class="container-fluid dashboard-container">
    <header class="dashboard-header">
        <div>
            <p class="dashboard-eyebrow">Panel administrativo</p>
            <h1>
                Hola, <?php echo htmlspecialchars(
                    $nombre,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?> <span>👋</span>
            </h1>
            <p>Resumen general de la actividad del gimnasio.</p>
        </div>
        <div class="dashboard-user-area">
            <div class="dashboard-user-info">
                <span class="dashboard-user-icon">
                    <i class="fas fa-user"></i>
                </span>
                <div>
                    <strong>
                        <?php echo htmlspecialchars(
                            $nombre,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </strong>
                    <small>
                        <?php echo htmlspecialchars(
                            $rolNombre,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </small>
                </div>
            </div>
            <a href="php_action/logout.php" class="dashboard-logout" title="Cerrar sesión">
                <i class="fas fa-right-from-bracket"></i>
                <span>Salir</span>
            </a>
        </div>
    </header>

    <?php
    $miembrosVencidos = $personaRepo->listarVencidas();

    $totalVencidos = count(
        $miembrosVencidos
    );
    ?>

    <section class="dashboard-stats-grid">
        <article class="dashboard-stat-card stat-sales">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <span>Ingresos hoy</span>
                <strong>
                    $<?php echo number_format(
                        $total_ventas_hoy,
                        2
                    ); ?>
                </strong>
                <small>Ventas registradas durante el día</small>
            </div>
        </article>

        <article class="dashboard-stat-card stat-visits">
            <div class="stat-icon">
                <i class="fas fa-person-walking"></i>
            </div>
            <div class="stat-content">
                <span>Visitas hoy</span>
                <strong>
                    <?php echo intval(
                        $total_visitas_hoy
                    ); ?>
                </strong>
                <small>Visitantes registrados</small>
            </div>
        </article>

        <article class="dashboard-stat-card stat-payments">
            <div class="stat-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-content">
                <span>Comprobantes</span>
                <strong>
                    <?php echo intval(
                        $totalPendientes
                    ); ?>
                </strong>
                <small>Pendientes de revisión</small>
            </div>
        </article>

        <article class="dashboard-stat-card stat-expired">
            <div class="stat-icon">
                <i class="fas fa-calendar-xmark"></i>
            </div>
            <div class="stat-content">
                <span>Membresías vencidas</span>
                <strong>
                    <?php echo $totalVencidos; ?>
                </strong>
                <small>Clientes que requieren renovación</small>
            </div>
        </article>
    </section>

    <section class="dashboard-main-grid">
        <article class="dashboard-panel quick-actions-panel">
            <div class="panel-header">
                <div>
                    <h2>Acciones rápidas</h2>
                    <p>Operaciones frecuentes del sistema</p>
                </div>
                <span class="panel-header-icon">
                    <i class="fas fa-bolt"></i>
                </span>
            </div>

            <div class="quick-actions-grid">
                <button type="button" class="quick-action-button action-product" data-toggle="modal" data-target="#sellProductDashboardModal">
                    <span>
                        <i class="fas fa-box-open"></i>
                    </span>
                    <div>
                        <strong>Vender producto</strong>
                        <small>Registrar venta e inventario</small>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </button>

                <button type="button" class="quick-action-button action-towel" data-toggle="modal" data-target="#rentTowelModal">
                    <span>
                        <i class="fas fa-tshirt"></i>
                    </span>
                    <div>
                        <strong>Rentar toalla</strong>
                        <small>Registrar renta del día</small>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </button>

                <button type="button" class="quick-action-button action-visit" data-toggle="modal" data-target="#newVisitModal">
                    <span>
                        <i class="fas fa-user-plus"></i>
                    </span>
                    <div>
                        <strong>Nueva visita</strong>
                        <small>Registrar un visitante</small>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </button>

                <a href="visitantes.php" class="quick-action-button action-history">
                    <span>
                        <i class="fas fa-clock-rotate-left"></i>
                    </span>
                    <div>
                        <strong>Historial de visitas</strong>
                        <small>Consultar registros anteriores</small>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </a>

                <?php if (
                    isRole('Administrador')
                    || isRole('Dueño')
                ): ?>
                    <a href="reportes.php" class="quick-action-button action-reports">
                        <span>
                            <i class="fas fa-chart-line"></i>
                        </span>
                        <div>
                            <strong>Reportes</strong>
                            <small>Consultar ingresos y actividad</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </article>

        <?php include __DIR__ . '/includes/partials/pagina_seccion_pendientes_panel.php'; ?>
    </section>

    <?php include __DIR__ . '/includes/partials/pagina_seccion_pendientes_lista.php'; ?>

    <?php include __DIR__ . '/includes/partials/pagina_seccion_vencidas.php'; ?>

</div>

<?php include __DIR__ . '/includes/partials/pagina_modales.php'; ?>
<!--Footer-->
<?php include 'includes/footer.php' ?>

<!--SCRIPTS JS-->
<script src="js/alerts.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/personas/add_person.js"></script>

</body>
</html>

<?php

$connect->close();

?>
