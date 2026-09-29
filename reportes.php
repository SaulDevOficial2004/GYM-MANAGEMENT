<?php

require_once __DIR__ . '/includes/auth.php';

requireRoles([
    'Administrador',
    'Dueño'
]);

$telefono = $_SESSION['telefono'];
$nombre = $_SESSION['nombre'];

require_once 'php_action/conn_db.php';

use GMS\Repository\ReporteRepository;

$reporteRepo = new ReporteRepository($connect);

// TOTAL HOY / MES / AÑO

$totalHoy = $reporteRepo->totalPorPeriodo('hoy');
$totalMes = $reporteRepo->totalPorPeriodo('mes');
$totalAnio = $reporteRepo->totalPorPeriodo('anio');

//MEMBRESIAS ---------------------->
$membresiasHoy = $reporteRepo->totalPorTipoPeriodo('MEMBRESIA', 'hoy');
$membresiasMes = $reporteRepo->totalPorTipoPeriodo('MEMBRESIA', 'mes');
$membresiasAnio = $reporteRepo->totalPorTipoPeriodo('MEMBRESIA', 'anio');
$totalMembresias = $reporteRepo->totalPorTipoPeriodo('MEMBRESIA', 'todo');
//FIN MEMBRESIAS -------------------->

//PRODUCTOS ------------------------->
$productosHoy = $reporteRepo->totalPorTipoPeriodo('PRODUCTO', 'hoy');
$productosMes = $reporteRepo->totalPorTipoPeriodo('PRODUCTO', 'mes');
$productosAnio = $reporteRepo->totalPorTipoPeriodo('PRODUCTO', 'anio');
$totalProductos = $reporteRepo->totalPorTipoPeriodo('PRODUCTO', 'todo');
//FIN PRODUCTOS ---------------------->

//VISITAS ---------------------------->
$visitasHoy = $reporteRepo->totalPorTipoPeriodo('VISITA', 'hoy');
$visitasMes = $reporteRepo->totalPorTipoPeriodo('VISITA', 'mes');
$visitasAnio = $reporteRepo->totalPorTipoPeriodo('VISITA', 'anio');
$totalVisitas = $reporteRepo->totalPorTipoPeriodo('VISITA', 'todo');
//FIN VISITAS ----------------------->

//TOALLAS --------------------------->
$toallasHoy = $reporteRepo->totalPorTipoPeriodo('TOALLA', 'hoy');
$toallasMes = $reporteRepo->totalPorTipoPeriodo('TOALLA', 'mes');
$toallasAnio = $reporteRepo->totalPorTipoPeriodo('TOALLA', 'anio');
$totalToallas = $reporteRepo->totalPorTipoPeriodo('TOALLA', 'todo');
//FIN TOALLAS ----------------------->

//USUARIOS PARA BITÁCORA

$usuariosBitacora = $reporteRepo->usuariosBitacora();


//MÓDULOS PARA BITÁCORA

$modulosBitacora = $reporteRepo->modulosBitacora();


//ACCIONES PARA BITÁCORA

$accionesBitacora = $reporteRepo->accionesBitacora();

/* ========================================= */
/* INDICADORES EJECUTIVOS */
/* ========================================= */

$diaActualDelMes = (int) date('j');

$promedioDiarioMes = $diaActualDelMes > 0
    ? (float) $totalMes / $diaActualDelMes
    : 0;

/* INGRESO HISTÓRICO GENERAL */

$totalHistorico =
    (float) $totalMembresias
    + (float) $totalProductos
    + (float) $totalVisitas
    + (float) $totalToallas;

/* PORCENTAJES HISTÓRICOS POR CATEGORÍA */

$porcentajeMembresias = $totalHistorico > 0
    ? ((float) $totalMembresias / $totalHistorico) * 100
    : 0;

$porcentajeProductos = $totalHistorico > 0
    ? ((float) $totalProductos / $totalHistorico) * 100
    : 0;

$porcentajeVisitas = $totalHistorico > 0
    ? ((float) $totalVisitas / $totalHistorico) * 100
    : 0;

$porcentajeToallas = $totalHistorico > 0
    ? ((float) $totalToallas / $totalHistorico) * 100
    : 0;

/* CATEGORÍA CON MAYOR INGRESO HISTÓRICO */

$categoriasIngresos = [
    'Membresías' => (float) $totalMembresias,
    'Productos' => (float) $totalProductos,
    'Visitas' => (float) $totalVisitas,
    'Toallas' => (float) $totalToallas
];

arsort(
    $categoriasIngresos
);

$categoriaPrincipal = array_key_first(
    $categoriasIngresos
);

$ingresoCategoriaPrincipal = reset(
    $categoriasIngresos
);

/* FECHA DE ACTUALIZACIÓN */

$mesesEspanol = [
    1 => 'enero',
    2 => 'febrero',
    3 => 'marzo',
    4 => 'abril',
    5 => 'mayo',
    6 => 'junio',
    7 => 'julio',
    8 => 'agosto',
    9 => 'septiembre',
    10 => 'octubre',
    11 => 'noviembre',
    12 => 'diciembre'
];

$fechaActualReporte =
    date('d')
    . ' de '
    . $mesesEspanol[(int) date('n')]
    . ' de '
    . date('Y');

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <title>
        Reportes | GMS
    </title>

    <?php include 'includes/head.php'; ?>

    <link
        rel="stylesheet"
        href="css/reportes.css">

</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="reports-container">
    <header class="reports-hero">
        <div class="reports-hero-content">
            <div class="reports-hero-heading">
                <span class="reports-hero-icon">
                    <i class="fas fa-chart-line"></i>
                </span>
                <div>
                    <p class="reports-eyebrow">Inteligencia financiera</p>
                    <h1>Reportes y análisis</h1>
                    <p class="reports-hero-description">Supervisa los ingresos, tendencias y actividad operativa de ProfitnessGym.</p>
                </div>
            </div>
            <div class="reports-hero-meta">
                <span>
                    <i class="fas fa-calendar-check"></i>
                    Actualizado el <?php echo $fechaActualReporte; ?>
                </span>
                <span>
                    <i class="fas fa-shield-halved"></i>
                    Información exclusiva de administración
                </span>
            </div>
        </div>
        <div class="reports-hero-actions">
            <button type="button" class="reports-action-button reports-refresh-button" id="refreshReportsData">
                <i class="fas fa-rotate"></i>
                <span>Actualizar</span>
            </button>
            <button type="button" class="reports-action-button reports-print-button" id="printFinancialReport">
                <i class="fas fa-print"></i>
                <span>Imprimir reporte</span>
            </button>
        </div>
    </header>

    <section class="reports-kpi-grid">
        <article class="report-kpi-card kpi-today">
            <div class="report-kpi-header">
                <span class="report-kpi-icon">
                    <i class="fas fa-calendar-day"></i>
                </span>
                <span class="report-kpi-period">Hoy</span>
            </div>
            <div class="report-kpi-content">
                <small>Ingresos de hoy</small>
                <strong>$<?php echo number_format($totalHoy,2); ?></strong>
                <p>Ventas registradas durante el día actual.</p>
            </div>
            <div class="report-kpi-footer">
                <span>
                    <i class="fas fa-clock"></i>
                    Información en tiempo real
                </span>
            </div>
        </article>

        <article class="report-kpi-card kpi-month">
            <div class="report-kpi-header">
                <span class="report-kpi-icon">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <span class="report-kpi-period">Mes actual</span>
            </div>
            <div class="report-kpi-content">
                <small>Ingresos del mes</small>
                <strong>$<?php echo number_format($totalMes,2); ?></strong>
                <p>Acumulado durante <?php echo ucfirst($mesesEspanol[(int) date('n')]); ?>.</p>
            </div>
            <div class="report-kpi-footer">
                <span>
                    <i class="fas fa-chart-line"></i>
                    <?php echo $diaActualDelMes; ?> días transcurridos
                </span>
            </div>
        </article>

        <article class="report-kpi-card kpi-year">
            <div class="report-kpi-header">
                <span class="report-kpi-icon">
                    <i class="fas fa-chart-column"></i>
                </span>
                <span class="report-kpi-period"><?php echo date('Y'); ?></span>
            </div>
            <div class="report-kpi-content">
                <small>Ingresos del año</small>
                <strong>$<?php echo number_format($totalAnio,2); ?></strong>
                <p>Total acumulado durante el año en curso.</p>
            </div>
            <div class="report-kpi-footer">
                <span>
                    <i class="fas fa-chart-simple"></i>
                    Rendimiento anual
                </span>
            </div>
        </article>

        <article class="report-kpi-card kpi-average">
            <div class="report-kpi-header">
                <span class="report-kpi-icon">
                    <i class="fas fa-calculator"></i>
                </span>
                <span class="report-kpi-period">Promedio</span>
            </div>
            <div class="report-kpi-content">
                <small>Promedio diario del mes</small>
                <strong>$<?php echo number_format($promedioDiarioMes,2); ?></strong>
                <p>Ingreso promedio por día durante el mes actual.</p>
            </div>
            <div class="report-kpi-footer">
                <span>
                    <i class="fas fa-divide"></i>
                    Basado en días transcurridos
                </span>
            </div>
        </article>
    </section>

    <section class="financial-overview">
        <div class="financial-overview-main">
            <div class="financial-overview-heading">
                <div>
                    <p class="reports-section-eyebrow">Panorama general</p>
                    <h2>Resumen financiero</h2>
                    <p>Consulta la composición histórica de los ingresos del gimnasio.</p>
                </div>
            </div>
            <div class="financial-overview-total">
                <small>Ingreso histórico registrado</small>
                <strong>$<?php echo number_format($totalHistorico,2); ?></strong>
                <span>
                    <i class="fas fa-database"></i>
                    Suma de todas las categorías
                </span>
            </div>
        </div>

        <div class="financial-highlight">
            <span class="financial-highlight-icon">
                <i class="fas fa-trophy"></i>
            </span>
            <div>
                <small>Categoría con mayor ingreso</small>
                <strong><?php echo htmlspecialchars($categoriaPrincipal,ENT_QUOTES,'UTF-8'); ?></strong>
                <p>$<?php echo number_format($ingresoCategoriaPrincipal,2); ?> acumulados históricamente.</p>
            </div>
        </div>
    </section>

    <section class="income-breakdown-section">
        <div class="reports-section-header">
            <div>
                <p class="reports-section-eyebrow">Composición de ingresos</p>
                <h2>Rendimiento por categoría</h2>
                <p>Analiza cuánto aporta cada área a los ingresos registrados.</p>
            </div>
            <span class="reports-section-badge">
                <i class="fas fa-layer-group"></i>
                4 categorías
            </span>
        </div>

        <div class="income-category-grid">
            <article class="income-category-card category-memberships">
                <div class="income-category-header">
                    <span class="income-category-icon">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <div class="income-category-title">
                        <h3>Membresías</h3>
                        <span>Ingresos por planes</span>
                    </div>
                    <span class="income-category-percentage"><?php echo number_format($porcentajeMembresias,1); ?>%</span>
                </div>
                <div class="income-category-primary">
                    <small>Ingreso del mes</small>
                    <strong>$<?php echo number_format($membresiasMes,2); ?></strong>
                </div>
                <div class="income-progress">
                    <span style="width:<?php echo min($porcentajeMembresias,100); ?>%"></span>
                </div>
                <div class="income-period-grid">
                    <div>
                        <small>Hoy</small>
                        <strong>$<?php echo number_format($membresiasHoy,2); ?></strong>
                    </div>
                    <div>
                        <small>Año</small>
                        <strong>$<?php echo number_format($membresiasAnio,2); ?></strong>
                    </div>
                    <div>
                        <small>Histórico</small>
                        <strong>$<?php echo number_format($totalMembresias,2); ?></strong>
                    </div>
                </div>
            </article>

            <article class="income-category-card category-products">
                <div class="income-category-header">
                    <span class="income-category-icon">
                        <i class="fas fa-box"></i>
                    </span>
                    <div class="income-category-title">
                        <h3>Productos</h3>
                        <span>Ventas de inventario</span>
                    </div>
                    <span class="income-category-percentage"><?php echo number_format($porcentajeProductos,1); ?>%</span>
                </div>
                <div class="income-category-primary">
                    <small>Ingreso del mes</small>
                    <strong>$<?php echo number_format($productosMes,2); ?></strong>
                </div>
                <div class="income-progress">
                    <span style="width:<?php echo min($porcentajeProductos,100); ?>%"></span>
                </div>
                <div class="income-period-grid">
                    <div>
                        <small>Hoy</small>
                        <strong>$<?php echo number_format($productosHoy,2); ?></strong>
                    </div>
                    <div>
                        <small>Año</small>
                        <strong>$<?php echo number_format($productosAnio,2); ?></strong>
                    </div>
                    <div>
                        <small>Histórico</small>
                        <strong>$<?php echo number_format($totalProductos,2); ?></strong>
                    </div>
                </div>
            </article>

            <article class="income-category-card category-visits">
                <div class="income-category-header">
                    <span class="income-category-icon">
                        <i class="fas fa-user-clock"></i>
                    </span>
                    <div class="income-category-title">
                        <h3>Visitas</h3>
                        <span>Accesos de visitantes</span>
                    </div>
                    <span class="income-category-percentage"><?php echo number_format($porcentajeVisitas,1); ?>%</span>
                </div>
                <div class="income-category-primary">
                    <small>Ingreso del mes</small>
                    <strong>$<?php echo number_format($visitasMes,2); ?></strong>
                </div>
                <div class="income-progress">
                    <span style="width:<?php echo min($porcentajeVisitas,100); ?>%"></span>
                </div>
                <div class="income-period-grid">
                    <div>
                        <small>Hoy</small>
                        <strong>$<?php echo number_format($visitasHoy,2); ?></strong>
                    </div>
                    <div>
                        <small>Año</small>
                        <strong>$<?php echo number_format($visitasAnio,2); ?></strong>
                    </div>
                    <div>
                        <small>Histórico</small>
                        <strong>$<?php echo number_format($totalVisitas,2); ?></strong>
                    </div>
                </div>
            </article>

            <article class="income-category-card category-towels">
                <div class="income-category-header">
                    <span class="income-category-icon">
                        <i class="fas fa-shirt"></i>
                    </span>
                    <div class="income-category-title">
                        <h3>Toallas</h3>
                        <span>Servicio de renta</span>
                    </div>
                    <span class="income-category-percentage"><?php echo number_format($porcentajeToallas,1); ?>%</span>
                </div>
                <div class="income-category-primary">
                    <small>Ingreso del mes</small>
                    <strong>$<?php echo number_format($toallasMes,2); ?></strong>
                </div>
                <div class="income-progress">
                    <span style="width:<?php echo min($porcentajeToallas,100); ?>%"></span>
                </div>
                <div class="income-period-grid">
                    <div>
                        <small>Hoy</small>
                        <strong>$<?php echo number_format($toallasHoy,2); ?></strong>
                    </div>
                    <div>
                        <small>Año</small>
                        <strong>$<?php echo number_format($toallasAnio,2); ?></strong>
                    </div>
                    <div>
                        <small>Histórico</small>
                        <strong>$<?php echo number_format($totalToallas,2); ?></strong>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- ANÁLISIS FINANCIERO -->

    <section class="analytics-section">
        <div class="reports-section-header analytics-section-header">
            <div>
                <p class="reports-section-eyebrow">Inteligencia de negocio</p>
                <h2>Análisis financiero</h2>
                <p>Visualiza tendencias, distribución y rendimiento de los ingresos registrados.</p>
            </div>
            <span class="reports-section-badge">
                <i class="fas fa-chart-bar"></i>
                Datos en tiempo real
            </span>
        </div>

        <div class="reports-charts-grid">
            <article class="card-custom report-chart-card">
                <div class="report-chart-header">
                    <div class="report-chart-title">
                        <span class="report-chart-icon chart-icon-line">
                            <i class="fas fa-chart-line"></i>
                        </span>
                        <div>
                            <span class="report-chart-eyebrow">Tendencia reciente</span>
                            <h3>Ventas de los últimos 7 días</h3>
                            <p>Comportamiento diario de los ingresos registrados.</p>
                        </div>
                    </div>
                    <div class="chart-summary">
                        <span>Total del periodo</span>
                        <strong id="lastSevenDaysTotal">$0.00</strong>
                    </div>
                </div>
                <div class="chart-insight">
                    <span>
                        <i class="fas fa-info-circle"></i>
                        Evolución diaria
                    </span>
                    <small>Los importes incluyen todas las categorías de venta.</small>
                </div>
                <div class="chart-container">
                    <canvas id="lastSevenDaysChart"></canvas>
                    <div class="chart-loading" id="lastSevenDaysLoading">
                        <span class="chart-loading-icon">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                        <div>
                            <strong>Cargando gráfica</strong>
                            <small>Consultando ventas recientes...</small>
                        </div>
                    </div>
                </div>
            </article>

            <article class="card-custom report-chart-card">
                <div class="report-chart-header">
                    <div class="report-chart-title">
                        <span class="report-chart-icon chart-icon-bars">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        <div>
                            <span class="report-chart-eyebrow">Rendimiento anual</span>
                            <h3>Ventas mensuales del año</h3>
                            <p>Compara los ingresos obtenidos durante cada mes.</p>
                        </div>
                    </div>
                    <div class="chart-summary">
                        <span id="monthlySalesYear">Total anual</span>
                        <strong id="monthlySalesTotal">$0.00</strong>
                    </div>
                </div>
                <div class="chart-insight">
                    <span>
                        <i class="fas fa-calendar-alt"></i>
                        Comparativo mensual
                    </span>
                    <small>Permite identificar meses con mayor actividad comercial.</small>
                </div>
                <div class="chart-container">
                    <canvas id="monthlySalesChart"></canvas>
                    <div class="chart-loading" id="monthlySalesLoading">
                        <span class="chart-loading-icon">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                        <div>
                            <strong>Cargando gráfica</strong>
                            <small>Consultando ventas mensuales...</small>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="reports-secondary-grid">
            <article class="card-custom report-chart-card distribution-card">
                <div class="report-chart-header">
                    <div class="report-chart-title">
                        <span class="report-chart-icon chart-icon-distribution">
                            <i class="fas fa-chart-pie"></i>
                        </span>
                        <div>
                            <span class="report-chart-eyebrow">Composición histórica</span>
                            <h3>Distribución de ingresos</h3>
                            <p>Participación de cada tipo de venta en el ingreso total.</p>
                        </div>
                    </div>
                    <div class="chart-summary">
                        <span>Ingreso total</span>
                        <strong id="salesDistributionTotal">$0.00</strong>
                    </div>
                </div>
                <div class="distribution-chart-layout">
                    <div class="distribution-chart-panel">
                        <div class="distribution-chart-container">
                            <canvas id="salesDistributionChart"></canvas>
                            <div class="chart-loading" id="salesDistributionLoading">
                                <span class="chart-loading-icon">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <div>
                                    <strong>Cargando distribución</strong>
                                    <small>Analizando las categorías...</small>
                                </div>
                            </div>
                        </div>
                        <div class="distribution-chart-caption">
                            <i class="fas fa-circle-notch"></i>
                            Distribución histórica
                        </div>
                    </div>
                    <div class="distribution-content">
                        <div class="distribution-content-header">
                            <div>
                                <h4>Participación por categoría</h4>
                                <p>Porcentaje e ingreso generado por cada área.</p>
                            </div>
                            <span>
                                <i class="fas fa-layer-group"></i>
                            </span>
                        </div>
                        <div class="distribution-summary" id="salesDistributionSummary"></div>
                    </div>
                </div>
            </article>
        </div>

        <div class="reports-secondary-grid">
            <article class="card-custom report-chart-card user-performance-card">
                <div class="report-chart-header">
                    <div class="report-chart-title">
                        <span class="report-chart-icon chart-icon-users">
                            <i class="fas fa-users"></i>
                        </span>
                        <div>
                            <span class="report-chart-eyebrow">Desempeño operativo</span>
                            <h3>Ventas por responsable</h3>
                            <p>Compara las operaciones registradas por cada usuario.</p>
                        </div>
                    </div>
                    <div class="chart-summary">
                        <span>Total del periodo</span>
                        <strong id="salesByUserTotal">$0.00</strong>
                    </div>
                </div>

                <div class="user-sales-toolbar">
                    <div class="user-sales-filters">
                        <button type="button" class="btn-user-sales-filter" data-period="today">
                            <i class="fas fa-calendar-day"></i>
                            Hoy
                        </button>
                        <button type="button" class="btn-user-sales-filter active" data-period="month">
                            <i class="fas fa-calendar-alt"></i>
                            Mes
                        </button>
                        <button type="button" class="btn-user-sales-filter" data-period="year">
                            <i class="fas fa-chart-line"></i>
                            Año
                        </button>
                        <button type="button" class="btn-user-sales-filter" data-period="all">
                            <i class="fas fa-history"></i>
                            Histórico
                        </button>
                    </div>
                    <div class="user-sales-operations">
                        <span class="user-sales-operations-icon">
                            <i class="fas fa-receipt"></i>
                        </span>
                        <div>
                            <strong id="salesByUserOperations">0</strong>
                            <small>operaciones</small>
                        </div>
                    </div>
                </div>

                <div class="user-sales-layout">
                    <div class="user-sales-chart-panel">
                        <div class="user-sales-chart-heading">
                            <div>
                                <h4>Ingresos por usuario</h4>
                                <p>Comparación visual del periodo seleccionado.</p>
                            </div>
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="user-sales-chart-container">
                            <canvas id="salesByUserChart"></canvas>
                            <div class="chart-loading" id="salesByUserLoading">
                                <span class="chart-loading-icon">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <div>
                                    <strong>Cargando responsables</strong>
                                    <small>Consultando operaciones...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="user-sales-table-panel">
                        <div class="user-sales-table-heading">
                            <div>
                                <h4>Detalle por responsable</h4>
                                <p>Operaciones, promedio e ingresos generados.</p>
                            </div>
                            <span>
                                <i class="fas fa-list"></i>
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table user-sales-table">
                                <thead>
                                    <tr>
                                        <th>Responsable</th>
                                        <th>Operaciones</th>
                                        <th>Promedio</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="salesByUserTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center">Cargando información...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="reports-secondary-grid">
            <article class="card-custom report-chart-card comparison-card">
                <div class="report-chart-header">
                    <div class="report-chart-title">
                        <span class="report-chart-icon chart-icon-comparison">
                            <i class="fas fa-balance-scale"></i>
                        </span>
                        <div>
                            <span class="report-chart-eyebrow">Comparación de periodos</span>
                            <h3>Comparativo de ventas</h3>
                            <p>Contrasta los ingresos de hoy, del mes y del año actual.</p>
                        </div>
                    </div>
                    <div class="chart-summary">
                        <span>Mayor periodo</span>
                        <strong id="salesComparisonHighest">$0.00</strong>
                    </div>
                </div>

                <div id="salesComparisonData" data-today="<?php echo floatval($totalHoy); ?>" data-month="<?php echo floatval($totalMes); ?>" data-year="<?php echo floatval($totalAnio); ?>"></div>

                <div class="sales-comparison-layout">
                    <div class="sales-comparison-chart-panel">
                        <div class="sales-comparison-chart-heading">
                            <div>
                                <h4>Ingresos acumulados</h4>
                                <p>Representación visual de los tres periodos.</p>
                            </div>
                            <span>
                                <i class="fas fa-chart-bar"></i>
                            </span>
                        </div>
                        <div class="sales-comparison-chart-container">
                            <canvas id="salesComparisonChart"></canvas>
                        </div>
                    </div>

                    <div class="sales-comparison-summary">
                        <div class="comparison-summary-item comparison-summary-today">
                            <span class="comparison-summary-icon comparison-today">
                                <i class="fas fa-calendar-day"></i>
                            </span>
                            <div>
                                <small>Ventas de hoy</small>
                                <strong>$<?php echo number_format($totalHoy,2); ?></strong>
                                <span>Periodo diario</span>
                            </div>
                        </div>
                        <div class="comparison-summary-item comparison-summary-month">
                            <span class="comparison-summary-icon comparison-month">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <div>
                                <small>Ventas del mes</small>
                                <strong>$<?php echo number_format($totalMes,2); ?></strong>
                                <span>Mes actual</span>
                            </div>
                        </div>
                        <div class="comparison-summary-item comparison-summary-year">
                            <span class="comparison-summary-icon comparison-year">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div>
                                <small>Ventas del año</small>
                                <strong>$<?php echo number_format($totalAnio,2); ?></strong>
                                <span>Año <?php echo date('Y'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- HISTORIAL GENERAL -->

    <section class="sales-history-card">
        <div class="sales-history-header">
            <div class="sales-history-title">
                <span class="sales-history-icon">
                    <i class="fas fa-receipt"></i>
                </span>
                <div>
                    <p class="reports-section-eyebrow">Registro de operaciones</p>
                    <h2>Historial general</h2>
                    <p>Consulta todas las ventas e ingresos registrados en el sistema.</p>
                </div>
            </div>
            <div class="sales-history-count">
                <span id="reportsTotalRecords">0</span>
                <small>operaciones encontradas</small>
            </div>
        </div>

        <div class="sales-history-toolbar">
            <div class="sales-history-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchReport" placeholder="Buscar por tipo, descripción o fecha" autocomplete="off">
                <button type="button" id="clearReportSearch" title="Limpiar búsqueda">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="report-filters">
                <button type="button" class="btn-report-filter active" data-filter="all">
                    <i class="fas fa-layer-group"></i>
                    Todas
                </button>
                <button type="button" class="btn-report-filter" data-filter="today">
                    <i class="fas fa-calendar-day"></i>
                    Hoy
                </button>
                <button type="button" class="btn-report-filter" data-filter="month">
                    <i class="fas fa-calendar-alt"></i>
                    Mes
                </button>
                <button type="button" class="btn-report-filter" data-filter="year">
                    <i class="fas fa-chart-line"></i>
                    Año
                </button>
            </div>
        </div>

        <div class="sales-history-table-container">
            <div class="table-responsive">
                <table class="table reports-table" id="reportsTable">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo de operación</th>
                            <th>Descripción</th>
                            <th class="reports-total-column">Total</th>
                        </tr>
                    </thead>
                    <tbody id="reportsTableBody">
                        <tr>
                            <td colspan="4" class="reports-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando historial...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="sales-history-empty d-none" id="reportsEmptyState">
                <span>
                    <i class="fas fa-search"></i>
                </span>
                <h3>No se encontraron operaciones</h3>
                <p>Prueba con otra búsqueda o selecciona un periodo diferente.</p>
            </div>
        </div>

        <div class="reports-pagination">
            <button type="button" class="reports-page-button" id="reportsPreviousPage" disabled>
                <i class="fas fa-chevron-left"></i>
                Anterior
            </button>

            <span id="reportsPageInformation">Página 1 de 1</span>

            <button type="button" class="reports-page-button" id="reportsNextPage" disabled>
                Siguiente
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <!-- BITÁCORA DE ACTIVIDAD -->

    <section class="audit-card">
        <div class="audit-header">
            <div class="audit-title">
                <span class="audit-title-icon">
                    <i class="fas fa-clipboard-list"></i>
                </span>
                <div>
                    <p class="reports-section-eyebrow">Control administrativo</p>
                    <h2>Bitácora de actividad</h2>
                    <p>Consulta los movimientos realizados por administradores, dueños y recepcionistas.</p>
                </div>
            </div>
            <div class="audit-total">
                <span class="audit-total-icon">
                    <i class="fas fa-list-ol"></i>
                </span>
                <div>
                    <strong id="auditTotalRecords">0</strong>
                    <small>movimientos encontrados</small>
                </div>
            </div>
        </div>

        <div class="audit-filter-panel">
            <div class="audit-filter-panel-header">
                <div>
                    <h3>Filtros de búsqueda</h3>
                    <p>Refina los resultados por responsable, módulo, acción o periodo.</p>
                </div>
                <span>
                    <i class="fas fa-filter"></i>
                </span>
            </div>

            <div class="audit-filters">
                <div class="audit-filter-group">
                    <label for="auditUserFilter">Usuario responsable</label>
                    <div class="audit-control-wrapper">
                        <i class="fas fa-user"></i>
                        <select id="auditUserFilter" class="audit-control">
                            <option value="0">Todos los usuarios</option>
                            <?php foreach($usuariosBitacora as $usuarioBitacora): ?>
                                <option value="<?php echo intval($usuarioBitacora['id']); ?>">
                                    <?php echo htmlspecialchars($usuarioBitacora['nombre'],ENT_QUOTES,'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="audit-filter-group">
                    <label for="auditModuleFilter">Módulo</label>
                    <div class="audit-control-wrapper">
                        <i class="fas fa-th-large"></i>
                        <select id="auditModuleFilter" class="audit-control">
                            <option value="">Todos los módulos</option>
                            <?php foreach($modulosBitacora as $moduloBitacora): ?>
                                <option value="<?php echo htmlspecialchars($moduloBitacora,ENT_QUOTES,'UTF-8'); ?>">
                                    <?php echo htmlspecialchars(ucfirst(strtolower($moduloBitacora)),ENT_QUOTES,'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="audit-filter-group">
                    <label for="auditActionFilter">Acción</label>
                    <div class="audit-control-wrapper">
                        <i class="fas fa-bolt"></i>
                        <select id="auditActionFilter" class="audit-control">
                            <option value="">Todas las acciones</option>
                            <?php foreach($accionesBitacora as $accionBitacora): ?>
                                <option value="<?php echo htmlspecialchars($accionBitacora,ENT_QUOTES,'UTF-8'); ?>">
                                    <?php echo htmlspecialchars(str_replace('_',' ',$accionBitacora),ENT_QUOTES,'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="audit-filter-group">
                    <label for="auditDateFrom">Desde</label>
                    <div class="audit-control-wrapper">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" id="auditDateFrom" class="audit-control">
                    </div>
                </div>

                <div class="audit-filter-group">
                    <label for="auditDateTo">Hasta</label>
                    <div class="audit-control-wrapper">
                        <i class="fas fa-calendar-check"></i>
                        <input type="date" id="auditDateTo" class="audit-control">
                    </div>
                </div>
            </div>

            <div class="audit-filter-actions">
                <div class="audit-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchAudit" placeholder="Buscar dentro de los resultados" autocomplete="off">
                    <button type="button" id="clearAuditSearch" title="Limpiar búsqueda">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="button" id="clearAuditFilters" class="btn-clear-audit">
                    <i class="fas fa-undo"></i>
                    Limpiar filtros
                </button>
            </div>
        </div>

        <div class="audit-table-container">
            <div class="table-responsive">
                <table class="table audit-table" id="auditTable">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Responsable</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Descripción</th>
                            <th>Registro</th>
                        </tr>
                    </thead>
                    <tbody id="auditTableBody">
                        <tr>
                            <td colspan="6" class="audit-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando bitácora...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="audit-pagination">
            <button type="button" id="auditPreviousPage" class="audit-page-button" disabled>
                <i class="fas fa-chevron-left"></i>
                Anterior
            </button>
            <span id="auditPageInformation">Página 1 de 1</span>
            <button type="button" id="auditNextPage" class="audit-page-button" disabled>
                Siguiente
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>
</main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"
            integrity="sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ"
            crossorigin="anonymous"></script>
    <script src="js/reportes.js"></script>

    </body>
    </html>

    <?php

    $connect->close();

    ?>
