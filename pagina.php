<?php
//SESION
session_start();

if(isset($_SESSION['telefono'])){

    $telefono = $_SESSION['telefono'];
    $nombre = $_SESSION['nombre'];

    require_once 'php_action/conn_db.php';

    //COMPROBANTES PENDIENTES

    $sqlComprobantes="

            SELECT
                cp.*,
                p.nombre,
                p.fecha_ini,
                p.fecha_fin,
                p.membresia_id,
                m.id AS membresia_id,
                m.nombre AS membresia,
                m.dias
            FROM comprobantes_pago cp
            INNER JOIN personas p
                ON p.id = cp.persona_id
            LEFT JOIN membresias m
                ON m.id = p.membresia_id
            WHERE cp.status = 'PENDIENTE'
            ORDER BY cp.fecha_subida ASC;

        ";

        $resultComprobantes = $connect->query($sqlComprobantes);
        $totalPendientes = $resultComprobantes->num_rows;

        /*=====================================
        COLOR DE LA CARD
        =====================================*/

        $cardColor = 'green';

        if($totalPendientes >= 6){

            $cardColor = 'red';

        }else if($totalPendientes >= 1){

            $cardColor = 'yellow';

        }

    //HISTORIAL COMPROBANTES

    $sqlHistorialComprobantes = "

        SELECT

            cp.*,
            p.nombre,
            p.folio,
            p.membresia_id,
            u.nombre AS revisado_por_nombre

        FROM comprobantes_pago cp
        INNER JOIN personas p
        ON p.id = cp.persona_id
        LEFT JOIN usuarios u
        ON u.id = cp.revisado_por
        ORDER BY cp.fecha_subida DESC

    ";

    $resultHistorialComprobantes = $connect->query($sqlHistorialComprobantes);

    $sqlMembresias = "
        SELECT *
        FROM membresias
        WHERE activo = 1
        ORDER BY dias ASC
    ";

    $resultMembresias = $connect->query(
        $sqlMembresias
    );

    $sql = "
        SELECT *
        FROM usuarios
        WHERE telefono = ?
        AND activo = 1
    ";

    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInformation = '';

    if($result->num_rows === 1){
        $row = $result->fetch_assoc();

        $telefonoUsuario = $row['telefono'];

    }

    $stmt->close();

}else{

    header("location:index.php");
    exit();

}

//VISITAS DE HOY
$sql_visitas_hoy = "
    SELECT COUNT(*) AS total
    FROM visitas
    WHERE DATE(fecha_visita) = CURDATE()
";

$result_visitas_hoy = $connect->query($sql_visitas_hoy);

$row_visitas_hoy = $result_visitas_hoy->fetch_assoc();

$total_visitas_hoy = $row_visitas_hoy['total'];

//VENTAS DE HOY
$sql_ventas_hoy = "
    SELECT
        IFNULL(SUM(total),0) AS total
    FROM ventas
    WHERE DATE(fecha_venta) = CURDATE()
";

$result_ventas_hoy = $connect->query($sql_ventas_hoy);
$row_ventas_hoy = $result_ventas_hoy->fetch_assoc();
$total_ventas_hoy = $row_ventas_hoy['total'];

//CONFIGURACIÓN TRANSFERENCIAS

$sqlConfiguracionTransferencias = "

    SELECT *
    FROM configuracion_transferencias
    LIMIT 1

";

$resultConfiguracionTransferencias = $connect->query($sqlConfiguracionTransferencias);
$configuracionTransferencias = $resultConfiguracionTransferencias->fetch_assoc();

?>

<!--INICIO DE HTML-->

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard | ProfitnessGym</title>

    <!--Links del head-->
    <?php include 'includes/head.php' ?>

    <!-- CSS PERSONALIZADO -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<!--Navbar general-->
<?php include 'includes/navbar.php' ?>

<!-- CONTENIDO -->
<div class="container-fluid dashboard-container">
    <div class="dashboard-grid">

        <!-- BIENVENIDA AL USUARIO-->
        <div class="card-custom user-card">
            <div class="welcome-header">
                <div>
                    <h1 class="welcome-title">

                        ¡Hola <?php echo $nombre; ?>!

                    </h1>
                    <p class="welcome-subtitle">

                        Bienvenido nuevamente al sistema administrativo.

                    </p>
                    <div class="user-badge">

                        <i class="fas fa-user-circle"></i>

                        <?php echo $nombre; ?>

                    </div>
                </div>

                <div class="welcome-right">
                    <a href="php_action/logout.php" class="btn btn-logout">
                        <i class="fas fa-right-from-bracket"></i>

                        Salir

                    </a>
                </div>
            </div>
        </div>

        <!--VENTAS Y VISITAS DEL DIA-->
        <div class="dashboard-bottom">

            <!--Ventas del dia-->
            <div class="card-custom">

                <h3>Ventas del día</h3>

                <h1 class="dashboard-number">
                    $<?php echo number_format(
                        $total_ventas_hoy,
                        2
                    ); ?>
                </h1>

                <p>
                    Ingresos generados hoy
                </p>

                <div class="dashboard-actions">

                    <button
                        class="btn-dashboard-action"
                        data-toggle="modal"
                        data-target="#sellProductDashboardModal">

                        <i class="fas fa-box-open"></i>
                        Vender Producto

                    </button>

                    <button
                        class="btn-dashboard-action"
                        data-toggle="modal"
                        data-target="#rentTowelModal">

                        <i class="fas fa-tshirt"></i>
                        Rentar Toalla

                    </button>

                    <a
                        href="reportes.php"
                        class="btn-dashboard-action">

                        <i class="fas fa-chart-line"></i>
                        Reportes

                    </a>

                </div>

            </div>

            <!--Visitas totales-->
            <div class="card-custom visits-card">
                <h3>Visitas Hoy</h3>
                <h1 class="dashboard-number">
                    <?php echo $total_visitas_hoy; ?>
                </h1>
                <p>
                    Visitantes registrados hoy
                </p>
                <div class="visits-actions">
                    <button
                        type="button"
                        class="btn btn-primary-action"
                        data-toggle="modal"
                        data-target="#newVisitModal">

                        <i class="fas fa-plus"></i>

                        Nueva visita
                    </button>

                    <a href="visitantes.php" class="btn btn-secondary-action">
                        <i class="fas fa-clock-rotate-left"></i>
                        Historial
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!--COMPROBANTES PENDIENTES-->
    <div class="card-custom pending-card <?php echo $cardColor; ?>">

        <div class="pending-header">

            <div>

                <h2>

                    <i class="fas fa-file-invoice-dollar"></i>

                    Comprobantes Pendientes

                </h2>

                <p>

                    Esperando revisión.

                </p>

            </div>

            <div>

                <h1>

                    <?php echo $totalPendientes; ?>

                </h1>

            </div>

        </div>

        <div class="pending-actions">

            <button
                class="btn-dashboard-action"
                id="showPendingPayments">

                <i class="fas fa-eye"></i>

                Ver comprobantes

            </button>

            <button
                class="btn-dashboard-action"

                data-toggle="modal"

                data-target="#paymentHistoryModal">

                <i class="fas fa-clock-rotate-left"></i>

                Historial

            </button>

            <button
                class="btn-dashboard-action"
                data-toggle="modal"
                data-target="#transferSettingsModal">

                <i class="fas fa-gear"></i>

                Configurar Cuenta de Transferencias

            </button>

        </div>

    </div>

    <!--MOSTRAR PENDIENTES-->
    <div
        id="pendingPaymentsContainer"
        style="display:none;">

        <div class="pending-grid">

        <?php

        if($totalPendientes > 0){

            while($row = $resultComprobantes->fetch_assoc()){

                ?>

                <div class="card-custom payment-card">

                    <div class="payment-top">

                        <div>

                            <h4>

                                <?php
                                echo $row['nombre'];
                                ?>

                            </h4>

                            <small>

                                <?php
                                echo $row['folio_cliente'];
                                ?>

                            </small>

                        </div>

                        <span class="status-pending">

                            Pendiente

                        </span>

                    </div>

                    <div class="payment-body">

                        <p>

                            <strong>

                                Membresía

                            </strong>

                            <br>

                            <?php
                            echo $row['membresia'];
                            ?>

                        </p>

                        <p>

                            <strong>

                                Concepto

                            </strong>

                            <br>

                            <?php

                            echo !empty($row['concepto'])

                            ?

                            $row['concepto']

                            :

                            'Sin concepto';

                            ?>

                        </p>

                        <p>

                            <strong>

                                Fecha

                            </strong>

                            <br>

                            <?php

                            echo date(

                                'd/m/Y H:i',

                                strtotime(
                                    $row['fecha_subida']
                                )

                            );

                            ?>

                        </p>

                    </div>

                    <button

                        class="btn-dashboard-action viewPaymentBtn"
                        data-id="<?php echo $row['id']; ?>"
                        data-persona="<?php echo $row['persona_id']; ?>"
                        data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                        data-folio="<?php echo $row['folio_cliente']; ?>"
                        data-membresia="<?php echo htmlspecialchars($row['membresia']); ?>"
                        data-dias="<?php echo $row['dias']; ?>"
                        data-concepto="<?php echo htmlspecialchars($row['concepto']); ?>"
                        data-status="<?php echo $row['status']; ?>"
                        data-fecha="<?php echo $row['fecha_subida']; ?>"
                        data-archivo="<?php echo $row['archivo']; ?>">

                            <i class="fas fa-eye"></i>

                            Ver comprobante

                    </button>

                </div>

                <?php

            }

        }else{

            ?>

            <div class="card-custom">

                <div class="text-center py-4">

                    <i
                        class="fas fa-check-circle"
                        style="font-size:60px;color:#28a745;">

                    </i>

                    <h4 class="mt-3">

                        No hay comprobantes pendientes.

                    </h4>

                </div>

            </div>

            <?php

        }

        ?>

        </div>

    </div>

    <!-- MEMBRESIAS VENCIDAS -->

    <?php

    $sql_vencidos = "
        SELECT * 
        FROM personas 
        WHERE fecha_fin < NOW()
        AND estatus = 1
    ";

    $result_vencidos = $connect->query($sql_vencidos);

    ?>

    <div class="card-custom">
        <div class="section-title-container">
            <h2 class="section-title">

                Membresías vencidas

            </h2>

            <p class="section-subtitle">

                Clientes que necesitan renovación de membresía

            </p>
        </div>

        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Vencimiento</th>
                        <th>Membresía</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>

                <?php

                while($row = $result_vencidos->fetch_assoc()){

                    $fechaFin = date("d/m/Y", strtotime($row['fecha_fin']));

                    echo '

                    <tr>
                        <td>'.$row['nombre'].'</td>
                        <td>'.$fechaFin.'</td>
                        <td>

                            <button
                                type="button"
                                class="btn btn-update updateBtn"

                                data-toggle="modal"
                                data-target="#updateMembershipModal"

                                data-id="'.$row['id'].'"
                                data-nombre="'.$row['nombre'].'">

                                <i class="fas fa-credit-card"></i>
                                Actualizar

                            </button>

                        </td>

                        <td>

                            <div class="status-actions">

                                <span class="status-expired">

                                    Vencido

                                </span>

                                    <a href="#" class="btn btn-delete disableMemberBtn" data-id="'.$row['id'].'">

                                        <i class="fas fa-trash"></i>

                                    </a>

                            </div>
                        </td>
                    </tr>
                    ';
                }

                ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL ACTUALIZAR MEMBRESIA -->

<div class="modal fade"
     id="updateMembershipModal"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered"
         role="document">

        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4 class="modal-title">

                    <i class="fas fa-credit-card"></i>
                    Actualizar Membresía

                </h4>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="updateMembershipForm"
                  method="POST">

                <div class="modal-body">

                    <input type="hidden"
                           name="id"
                           id="cliente_id">

                    <div class="client-name-box">

                        Cliente:
                        <span id="cliente_nombre"></span>

                    </div>

                    <div class="form-group">

                        <label>Duración</label>

                        <select
                            id="persona_membresia_edit"
                            class="form-control modern-input"
                            required>

                            <option value="">
                                Seleccione membresía
                            </option>

                            <?php while($membresia = $resultMembresias->fetch_assoc()){ ?>

                                <option
                                    value="<?php echo $membresia['id']; ?>"
                                    data-dias="<?php echo $membresia['dias']; ?>">

                                    <?php echo $membresia['nombre']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Fecha inicial</label>

                        <input type="date"
                               name="fecha_ini"
                               id="fecha_ini"
                               class="form-control modern-input"
                               required>

                    </div>

                    <div class="form-group">

                        <label>Fecha vencimiento</label>

                        <input type="date"
                               name="fecha_fin"
                               id="fecha_fin"
                               class="form-control modern-input"
                               required>

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="button"
                            class="btn btn-cancel"
                            data-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit"
                            class="btn btn-save">

                        Guardar Membresía

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- MODAL AGREGAR PERSONA -->
<?php include 'includes/modals/add_person_modal.php' ?>

<!-- MODAL NUEVA VISITA -->

<div class="modal fade" id="newVisitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4 class="modal-title">

                    Nueva Visita

                </h4>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Buscar visitante</label>
                    <input
                        type="text"
                        id="searchVisitante"
                        class="form-control modern-input"
                        placeholder="Escriba un nombre">
                </div>

                <div id="visitantesResults">

                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CREAR VISITANTE -->

<div class="modal fade" id="createVisitanteModal" tabindex="-1">
   <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4 class="modal-title">

                    Crear Visitante

                </h4>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>
                </button>
            </div>

            <form id="createVisitanteForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>

                        <input
                            type="text"
                            id="visitante_nombre"
                            class="form-control modern-input"
                            required>

                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button
                        type="submit"
                        class="btn btn-save">

                        Guardar

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CANTIDAD PRODUCTO -->

<div class="modal fade"
     id="sellProductQuantityModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    Confirmar Venta

                </h4>

            </div>

            <form id="sellProductDashboardForm">

                <input
                    type="hidden"
                    id="dashboard_producto_id">

                <div class="modal-body">

                    <div class="form-group">

                        <label>Producto</label>

                        <input
                            type="text"
                            id="dashboard_producto_nombre"
                            class="form-control modern-input"
                            readonly>

                    </div>

                    <div class="form-group">

                        <label>Stock Disponible</label>

                        <input
                            type="text"
                            id="dashboard_producto_stock"
                            class="form-control modern-input"
                            readonly>

                    </div>

                    <div class="form-group">

                        <label>Cantidad</label>

                        <input
                            type="number"
                            id="dashboard_producto_cantidad"
                            min="1"
                            value="1"
                            class="form-control modern-input">

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button
                        type="submit"
                        class="btn btn-save">

                        Vender

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- MODAL VENDER PRODUCTO DASHBOARD -->

<div class="modal fade"
     id="sellProductDashboardModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    Vender Producto

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="form-group">

                    <label>Buscar producto</label>

                    <input
                        type="text"
                        id="searchProductDashboard"
                        class="form-control modern-input"
                        placeholder="Escriba un producto">

                </div>

                <div id="productsResults">

                </div>

            </div>

        </div>

    </div>

</div>

<!-- MODAL RENTAR TOALLA -->

<div class="modal fade"
     id="rentTowelModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    Rentar Toalla

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="rentTowelForm">

                <div class="modal-body">

                    <div class="alert alert-info">

                        Precio de renta:
                        <strong>$25.00</strong>

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button
                        type="button"
                        class="btn btn-cancel"
                        data-dismiss="modal">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-save">

                        Registrar renta

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<div class="modal fade" id="paymentReviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h3>

                    <i class="fas fa-file-invoice-dollar"></i>

                    Revisión de Comprobante

                </h3>

                <button
                class="close"
                data-dismiss="modal">

                    <span>&times;</span>

                </button>
            </div>

            <input
                type="hidden"
                id="reviewComprobanteId">

            <div class="modal-body">
                <div class="review-grid">
                    <div>

                        <img
                            id="adminPreviewImage"
                            class="img-fluid rounded d-none">

                        <iframe
                            id="adminPreviewPdf"
                            class="d-none"
                            style="
                            width:100%;
                            height:600px;
                            border:none;
                            ">

                        </iframe>
                    </div>

                    <div>

                        <h4 id="reviewNombre"></h4>

                        <hr>

                        <p>

                            <strong>

                                Folio

                            </strong>

                            <br>

                            <span id="reviewFolio"></span>

                        </p>

                        <p>
                            <strong>

                                Membresía

                            </strong>
                        </p>

                        <select
                            id="reviewMembership"
                            class="form-control modern-input">

                                <?php

                                $sqlMembresias = "

                                    SELECT *
                                    FROM membresias
                                    WHERE activo = 1
                                    ORDER BY dias ASC

                                ";

                                $resultMembresias = $connect->query($sqlMembresias);

                                while($m = $resultMembresias->fetch_assoc()){

                                    $precio = $m['precio'];

                                    if($m['promocion'] == 1 && !empty($m['precio_promocion'])){

                                        $precio = $m['precio_promocion'];

                                    }

                                ?>

                                    <option
                                        value="<?php echo $m['id']; ?>">

                                        <?php

                                        echo $m['nombre'] . " - $" . number_format($precio, 2);

                                        ?>

                                    </option>

                                    <?php

                                    }

                                    ?>

                        </select>

                        <p>

                            <strong>

                                Concepto

                            </strong>

                            <br>

                            <span id="reviewConcepto"></span>

                        </p>

                        <p>

                            <strong>

                                Fecha

                            </strong>

                            <br>

                            <span id="reviewFecha"></span>

                        </p>

                        <p>

                            <strong>

                                Estado

                            </strong>

                            <br>

                            <span
                            id="reviewStatus"
                            class="status-pending">

                                Pendiente

                            </span>

                        </p>

                    </div>

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                class="btn btn-cancel"
                data-dismiss="modal">
                    Cerrar

                </button>

                <button
                id="rejectPaymentBtn"
                class="btn btn-delete">

                    Rechazar

                </button>

                <button
                id="confirmPaymentBtn"
                class="btn btn-save">

                    Confirmar Pago

                </button>

            </div>

        </div>

    </div>

</div>

<!-- HISTORIAL COMPROBANTES -->

<div class="modal fade" id="paymentHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">

                <h3>

                    <i class="fas fa-clock-rotate-left"></i>

                    Historial de Comprobantes

                </h3>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>

                                <tr>

                                    <th>Cliente</th>
                                    <th>Folio</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Revisó</th>
                                    <th>Acción</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php

                            while($row = $resultHistorialComprobantes->fetch_assoc()){

                                $status='';

                                    switch($row['status']){

                                        case 'PENDIENTE':

                                    $status="<span class='status-pending'>Pendiente</span>";

                                    break;

                                        case 'CONFIRMADO':

                                    $status="<span class='status-confirmed'>Confirmado</span>";

                                    break;

                                        case 'RECHAZADO':

                                    $status="<span class='status-rejected'>Rechazado</span>";

                                    break;

                            }

                            ?>

                                <tr>

                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['folio_cliente']; ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_subida'])); ?></td>
                                    <td><?php echo $row['revisado_por_nombre'] ?? 'Pendiente'; ?></td>

                                    <td>

                                        <button
                                        class="btn btn-dashboard-action viewHistoryPaymentBtn"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-persona="<?php echo $row['persona_id']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                                        data-folio="<?php echo $row['folio_cliente']; ?>"
                                        data-membresiaid="<?php echo $row['membresia_id']; ?>"
                                        data-concepto="<?php echo htmlspecialchars($row['concepto']); ?>"
                                        data-status="<?php echo $row['status']; ?>"
                                        data-fecha="<?php echo $row['fecha_subida']; ?>"
                                        data-archivo="<?php echo $row['archivo']; ?>"
                                        data-motivo="<?php echo htmlspecialchars($row['motivo_rechazo']); ?>">

                                            <i class="fas fa-eye"></i>

                                            Ver

                                        </button>

                                    </td>

                                </tr>

                                <?php

                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer border-0">

                    <button
                    class="btn btn-cancel"
                    data-dismiss="modal">

                        Cerrar

                    </button>

                </div>

        </div>

    </div>

</div>

<div class="modal fade" id="paymentHistoryViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">

                <h3>

                    Ver comprobante

                </h3>

                <button
                class="close"
                data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">
                <div class="review-grid">
                    <div>
                        <img
                        id="historyPreviewImage"
                        class="img-fluid rounded d-none">

                        <iframe
                        id="historyPreviewPdf"
                        class="d-none"
                        style="width:100%;height:600px;border:none;">
                        </iframe>
                    </div>

                    <div>

                        <h4 id="historyNombre"></h4>

                        <hr>

                        <p>

                            <strong>Folio</strong>

                            <br>

                            <span id="historyFolio"></span>

                        </p>

                        <p>

                            <strong>Concepto</strong>

                            <br>

                            <span id="historyConcepto"></span>

                        </p>

                            <p>

                            <strong>Fecha</strong>

                            <br>

                            <span id="historyFecha"></span>

                        </p>

                        <p>

                            <strong>Estado</strong>

                            <br>

                            <span id="historyEstado"></span>

                        </p>

                        <p id="historyMotivoContainer" class="d-none">

                            <strong>Motivo</strong>

                            <br>

                            <span id="historyMotivo"></span>

                        </p>

                    </div>

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                class="btn btn-cancel"
                data-dismiss="modal">

                    Cerrar

                </button>

            </div>

        </div>

    </div>

</div>

<!-- MODAL CONFIGURACIÓN TRANSFERENCIAS -->

<div class="modal fade"
     id="transferSettingsModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    <i class="fas fa-university"></i>

                    Configuración Bancaria

                </h4>

                <button
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="transferSettingsForm">

                <input
                    type="hidden"
                    id="transfer_config_id"
                    value="<?php echo $configuracionTransferencias['id']; ?>">

                <div class="modal-body">

                    <div class="form-group">

                        <label>

                            Banco

                        </label>

                        <input
                            type="text"
                            id="transfer_banco"
                            class="form-control modern-input"
                            value="<?php echo htmlspecialchars($configuracionTransferencias['banco']); ?>">

                    </div>

                    <div class="form-group">

                        <label>

                            Titular

                        </label>

                        <input
                            type="text"
                            id="transfer_titular"
                            class="form-control modern-input"
                            value="<?php echo htmlspecialchars($configuracionTransferencias['titular']); ?>">

                    </div>

                    <div class="form-group">

                        <label>

                            CLABE

                        </label>

                        <input
                            type="text"
                            id="transfer_clabe"
                            class="form-control modern-input"
                            value="<?php echo htmlspecialchars($configuracionTransferencias['clabe']); ?>">

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button
                        type="button"
                        class="btn btn-cancel"
                        data-dismiss="modal">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-save">

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!--Footer-->
<?php include 'includes/footer.php' ?>

<!--SCRIPTS JS-->
<script src="js/alerts.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/personas/add_person.js"></script>

<!--Funcionalidades-->
<?php if(isset($_GET['success'])): ?>

<script>

    loginSuccess();

</script>

<?php endif; ?>

<?php if(isset($_GET['update'])): ?>

    <script>

        updateMembership();

    </script>

<?php endif; ?>

</body>
</html>

<?php

$connect->close();

?>