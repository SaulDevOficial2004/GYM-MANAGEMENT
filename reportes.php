<?php

session_start();

if(!isset($_SESSION['telefono'])){

    header("Location:index.php");
    exit();

}

require_once 'php_action/conn_db.php';

// TOTAL HOY

$sqlHoy = "
    SELECT COALESCE(SUM(total),0) AS total
    FROM ventas
    WHERE DATE(created_at) = CURDATE()
";

$resultHoy = $connect->query($sqlHoy);
$totalHoy = $resultHoy->fetch_assoc()['total'];

// TOTAL MES

$sqlMes = "
    SELECT COALESCE(SUM(total),0) AS total
    FROM ventas
    WHERE MONTH(created_at) = MONTH(CURDATE())
    AND YEAR(created_at) = YEAR(CURDATE())
";

$resultMes = $connect->query($sqlMes);
$totalMes = $resultMes->fetch_assoc()['total'];

// TOTAL AÑO

$sqlAnio = "
    SELECT COALESCE(SUM(total),0) AS total
    FROM ventas
    WHERE YEAR(created_at) = YEAR(CURDATE())
";

$resultAnio = $connect->query($sqlAnio);
$totalAnio = $resultAnio->fetch_assoc()['total'];

//MEMBRESIAS ---------------------->
// HOY
$membresiasHoy = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='MEMBRESIA'
    AND DATE(created_at)=CURDATE()
")->fetch_assoc()['total'];

// MES
$membresiasMes = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='MEMBRESIA'
    AND MONTH(created_at)=MONTH(CURDATE())
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

// AÑO
$membresiasAnio = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='MEMBRESIA'
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$totalMembresias = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='MEMBRESIA'
")->fetch_assoc()['total'];
//FIN MEMBRESIAS -------------------->

//PRODUCTOS ------------------------->
$productosHoy = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='PRODUCTO'
    AND DATE(created_at)=CURDATE()
")->fetch_assoc()['total'];

$productosMes = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='PRODUCTO'
    AND MONTH(created_at)=MONTH(CURDATE())
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$productosAnio = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='PRODUCTO'
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$totalProductos = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='PRODUCTO'
")->fetch_assoc()['total'];
//FIN PRODUCTOS ---------------------->

//VISITAS ---------------------------->
$visitasHoy = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='VISITA'
    AND DATE(created_at)=CURDATE()
")->fetch_assoc()['total'];

$visitasMes = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='VISITA'
    AND MONTH(created_at)=MONTH(CURDATE())
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$visitasAnio = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='VISITA'
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$totalVisitas = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='VISITA'
")->fetch_assoc()['total'];
//FIN VISITAS ----------------------->

//TOALLAS --------------------------->
$toallasHoy = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='TOALLA'
    AND DATE(created_at)=CURDATE()
")->fetch_assoc()['total'];

$toallasMes = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='TOALLA'
    AND MONTH(created_at)=MONTH(CURDATE())
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$toallasAnio = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='TOALLA'
    AND YEAR(created_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];

$totalToallas = $connect->query("
    SELECT COALESCE(SUM(total),0) total
    FROM ventas
    WHERE tipo='TOALLA'
")->fetch_assoc()['total'];
//FIN TOALLAS ----------------------->

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <title>
        Reportes | ProfitnessGym
    </title>

    <?php include 'includes/head.php'; ?>

    <link
        rel="stylesheet"
        href="css/reportes.css">

</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container-fluid page-container">

    <div class="dashboard-grid">

        <div class="card-custom">

            <h3>
                <i class="fas fa-calendar-day"></i>
                Ventas Hoy
            </h3>

            <h1 class="dashboard-number">

                $<?php echo number_format(
                    $totalHoy,
                    2
                ); ?>

            </h1>

        </div>

        <div class="card-custom">

            <h3>
                <i class="fas fa-calendar-alt"></i>
                Ventas Mes
            </h3>

            <h1 class="dashboard-number">

                $<?php echo number_format(
                    $totalMes,
                    2
                ); ?>

            </h1>

        </div>

        <div class="card-custom">

            <h3>
                <i class="fas fa-chart-line"></i>
                Ventas Año
            </h3>

            <h1 class="dashboard-number">

                $<?php echo number_format(
                    $totalAnio,
                    2
                ); ?>

            </h1>

        </div>

    </div>

    <div class="report-category-grid">

    <!--CARD MEMBRESIAS-->
        <div class="card-custom report-mini-card">

            <h5>
                <i class="fas fa-credit-card"></i>
                Membresías
            </h5>

            <h3>
                <?php echo 'Hoy: $' . number_format($membresiasHoy,2); ?>
            </h3>

            <small>
                Mes:
                $<?php echo number_format($membresiasMes,2); ?>
            </small>

            <br>

            <small>
                Año:
                $<?php echo number_format($membresiasAnio,2); ?>
            </small>

            <br>

            <small>
                Histórica:
                $<?php echo number_format($totalMembresias,2); ?>
            </small>

        </div>

        <!--CARD PRODUCTOS-->
        <div class="card-custom report-mini-card">

            <h5>
                <i class="fas fa-box"></i>
                Productos
            </h5>

            <h3>
                <?php echo 'Hoy: $' . number_format($productosHoy,2); ?>
            </h3>

            <small>
                Mes:
                $<?php echo number_format($productosMes,2); ?>
            </small>

            <br>

            <small>
                Año:
                $<?php echo number_format($productosAnio,2); ?>
            </small>

            <br>

            <small>
                Histórica:
                $<?php echo number_format($totalProductos,2); ?>
            </small>

        </div>

        <!--CARD VISITAS-->
        <div class="card-custom report-mini-card">

            <h5>
                <i class="fas fa-user"></i>
                Visitas
            </h5>

            <h3>
                <?php echo 'Hoy: $' . number_format($visitasHoy,2); ?>
            </h3>

            <small>
                Mes:
                $<?php echo number_format($visitasMes,2); ?>
            </small>

            <br>

            <small>
                Año:
                $<?php echo number_format($visitasAnio,2); ?>
            </small>

            <br>

            <small>
                Histórica:
                $<?php echo number_format($totalVisitas,2); ?>
            </small>

        </div>

        <!--CARD TOALLAS-->
        <div class="card-custom report-mini-card">

            <h5>
                <i class="fas fa-tshirt"></i>
                Toallas
            </h5>

            <h3>
                <?php echo 'Hoy: $' . number_format($toallasHoy,2); ?>
            </h3>

            <small>
                Mes:
                $<?php echo number_format($toallasMes,2); ?>
            </small>

            <br>

            <small>
                Año:
                $<?php echo number_format($toallasAnio,2); ?>
            </small>

            <br>

            <small>
                Histórica:
                $<?php echo number_format($totalToallas,2); ?>
            </small>

        </div>

    </div>

    <div class="card-custom mt-4">

        <h3>

            Historial General

        </h3>

        <div class="search-box">

            <i class="fas fa-search"></i>

            <input
                type="text"
                id="searchReport"
                placeholder="Buscar venta">

        </div>

        <div class="report-filters">

            <button
                class="btn-report-filter active"
                data-filter="all">

                Todas

            </button>

            <button
                class="btn-report-filter"
                data-filter="today">

                Hoy

            </button>

            <button
                class="btn-report-filter"
                data-filter="month">

                Mes

            </button>

            <button
                class="btn-report-filter"
                data-filter="year">

                Año

            </button>

        </div>

        <div class="table-responsive">

            <table
                class="table custom-table"
                id="reportsTable">

                <thead>

                    <tr>

                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Total</th>

                    </tr>

                </thead>

                <tbody>
                <?php
                    $sqlVentas = "
                        SELECT *
                        FROM ventas
                        ORDER BY created_at DESC
                    ";

                    $resultVentas =
                        $connect->query($sqlVentas);

                    while(
                        $row =
                        $resultVentas->fetch_assoc()
                    ){

                        echo "

                            <tr data-date='{$row['created_at']}'>

                                <td>
                                    {$row['created_at']}
                                </td>

                                <td>
                                    {$row['tipo']}
                                </td>

                                <td>
                                    {$row['descripcion']}
                                </td>

                                <td>
                                    $" . number_format(
                                        $row['total'],
                                        2
                                    ) . "
                                </td>

                            </tr>

                        ";
                    }
                ?>
                </tbody>

            </table>

        </div>

    </div>

    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/reportes.js"></script>

    </body>
    </html>

    <?php

    $connect->close();

    ?>
