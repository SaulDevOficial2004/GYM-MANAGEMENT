<?php

require_once 'php_action/conn_db.php';

if(empty($_GET['folio'])){

    header("Location: inicio.php");
    exit();
}

$folio = trim($_GET['folio']);

$sqlPersona = "
    SELECT *
    FROM personas
    WHERE folio = ?
    LIMIT 1
";

$stmt = $connect->prepare($sqlPersona);

$stmt->bind_param(
    "s",
    $folio
);

$stmt->execute();

$resultPersona = $stmt->get_result();

if($resultPersona->num_rows == 0){

    header( "Location: inicio.php");
    exit();
}

$persona = $resultPersona->fetch_assoc();

//ESTADO MEMBRESIA
$fechaActual = new DateTime();
$fechaFin = new DateTime($persona['fecha_fin']);
$membresiaActiva = $fechaActual <= $fechaFin;

//HISTORIAL PENDIENTES
$sqlPendientes = "
    SELECT *
    FROM comprobantes_pago
    WHERE persona_id = ?
    AND status = 'PENDIENTE'
    ORDER BY fecha_subida DESC
";

$stmtPendientes = $connect->prepare($sqlPendientes);

$stmtPendientes->bind_param(
    "i",
    $persona['id']
);

$stmtPendientes->execute();
$pendientes = $stmtPendientes->get_result();

//HISTORIAL CONFIRMADOS
$sqlConfirmados = "
    SELECT *
    FROM comprobantes_pago
    WHERE persona_id = ?
    AND status = 'CONFIRMADO'
    ORDER BY fecha_subida DESC
";

$stmtConfirmados = $connect->prepare($sqlConfirmados);
$stmtConfirmados->bind_param(
    "i",
    $persona['id']
);

$stmtConfirmados->execute();
$confirmados = $stmtConfirmados->get_result();

//RECHAZADOS
$sqlRechazados = "

    SELECT *
    FROM comprobantes_pago
    WHERE persona_id = ?
    AND status = 'RECHAZADO'
    ORDER BY fecha_subida DESC

";

$stmtRechazados = $connect->prepare($sqlRechazados);
$stmtRechazados->bind_param("i",

    $persona['id']

);
$stmtRechazados->execute();
$resultRechazados = $stmtRechazados->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transferencias de <?php echo $persona['nombre'] ?></title>

    <?php include 'includes/head.php' ?>

    <link rel="stylesheet" href="css/transferencia_clientes.css">
</head>
<body>
    
<div class="container page-container">

    <div class="welcome-card">

        <div>

            <h2>

                <i class="fas fa-user-circle"></i>

                Hola <?php echo $persona['nombre']; ?>

            </h2>

            <p>

                Bienvenido al portal de pagos
                por transferencia.

            </p>

        </div>

        <div class="folio-box">

            <span>

                Folio Único

            </span>

            <strong>

                <?php echo $persona['folio']; ?>

            </strong>

        </div>

    </div>

    <div class="card-custom membership-status-card">

        <div class="status-header">

            <h4>

                <i class="fas fa-id-card"></i>

                Estado de Membresía

            </h4>

            <?php

            if($membresiaActiva){

                echo "

                <span
                class='status-active'>

                Activa

                </span>

                ";

            }else{

                echo "

                <span
                class='status-expired'>

                Vencida

                </span>

                ";

            }

            ?>

        </div>

        <div class="status-info">

            <div>

                <small>

                    Fecha de vencimiento

                </small>

                <h5>

                    <?php

                    echo date(
                        'd/m/Y',
                        strtotime(
                            $persona['fecha_fin']
                        )
                    );

                    ?>

                </h5>

            </div>

        </div>

    </div>

</div>

<?php if(!$membresiaActiva){ ?>

<div class="card-custom upload-card">

    <div class="upload-header">

        <h4>

            <i class="fas fa-cloud-upload-alt"></i>

            Subir Comprobante de Pago

        </h4>

        <p>

            Adjunta el comprobante de tu transferencia.
            Nuestro equipo revisará el pago y tu membresía se activará automáticamente una vez confirmado.

        </p>

    </div>

    <form
        id="uploadComprobanteForm"
        enctype="multipart/form-data">

        <input
            type="hidden"
            id="persona_id"
            name="persona_id"
            value="<?php echo $persona['id']; ?>">

        <input
            type="hidden"
            id="folio_cliente"
            name="folio_cliente"
            value="<?php echo $persona['folio']; ?>">

        <div class="form-group">

            <label>

                Concepto (Opcional)

            </label>

            <input
                type="text"
                id="concepto"
                name="concepto"
                class="form-control modern-input"
                placeholder="Ej. Pago mensualidad Julio">

        </div>

        <div class="form-group">

            <label>

                Comprobante

            </label>

            <input
                type="file"
                id="archivo"
                name="archivo"
                class="form-control modern-input"
                accept=".jpg,.jpeg,.png,.pdf"
                required>

            <small class="text-muted">

                Formatos permitidos:
                JPG, PNG o PDF.

            </small>

        </div>

        <button
            type="submit"
            class="btn btn-save btn-upload">

            <i class="fas fa-paper-plane"></i>

            Enviar Comprobante

        </button>

    </form>

</div>

<?php }else{ ?>

<div class="card-custom">

    <div class="text-center py-3">

        <i
            class="fas fa-check-circle"
            style="
                font-size:65px;
                color:#28a745;
                margin-bottom:20px;
            ">

        </i>

        <h3>

            Tu membresía está activa

        </h3>

        <p class="text-muted mb-0">

            No es necesario subir un comprobante
            de pago mientras tu membresía continúe vigente.

        </p>

    </div>

</div>

<?php } ?>

<!-- HISTORIAL -->

<div class="history-grid">

    <!-- PENDIENTES -->

    <div class="card-custom history-card">

        <h4>

            <i class="fas fa-clock"></i>

            Comprobantes Pendientes

        </h4>

        <?php

        if($pendientes->num_rows > 0){

            while($row = $pendientes->fetch_assoc()){

                ?>

                <div class="history-item">

                    <div>

                        <strong>

                            <?php

                            echo !empty($row['concepto'])

                            ?

                            $row['concepto']

                            :

                            'Sin concepto';

                            ?>

                        </strong>

                        <small>

                            <?php

                            echo date(

                                'd/m/Y H:i',

                                strtotime(
                                    $row['fecha_subida']
                                )

                            );

                            ?>

                        </small>

                    </div>

                    <div class="history-actions">

                        <span class="status-pending">

                            En revisión

                        </span>

                        <button
                            class="btn-view viewComprobanteBtn"
                            data-archivo="<?php echo $row['archivo']; ?>">

                            <i class="fas fa-eye"></i>

                            Ver

                        </button>

                    </div>

                </div>

                <?php

            }


        }else{

            ?>

            <div class="empty-history">

                <i class="fas fa-inbox"></i>

                <p>

                    No tienes comprobantes pendientes.

                </p>

            </div>

            <?php

        }

        ?>

    </div>

    <div class="card-custom mt-4">

        <div class="card-custom history-card">

            <h4>

                <i class="fas fa-times-circle"></i>

                Comprobantes Rechazados

            </h4>

        <?php

        if($resultRechazados->num_rows > 0){

            while($row = $resultRechazados->fetch_assoc()){

        ?>

            <div class="history-item">

                <div>

                    <strong>

                        <?php

                        echo !empty($row['concepto'])

                        ?

                        htmlspecialchars($row['concepto'])

                        :

                        'Sin concepto';

                        ?>

                    </strong>

                    <br>

                    <small>

                        <?php

                        echo date(

                            'd/m/Y H:i',

                            strtotime($row['fecha_subida'])

                        );

                        ?>

                    </small>

                    <br><br>

                    <small>

                        <strong>

                            Motivo:

                        </strong>

                        <br>

                        <?php

                        echo nl2br(

                            htmlspecialchars(

                                $row['motivo_rechazo']

                            )

                        );

                        ?>

                    </small>

                </div>

                <div class="history-actions">

                    <span class="status-rejected">

                        Rechazado

                    </span>

                    <button

                        class="btn-view viewComprobanteBtn"

                        data-archivo="<?php echo $row['archivo']; ?>">

                        <i class="fas fa-eye"></i>

                        Ver

                    </button>

                </div>

            </div>

        <?php

            }

        }else{

        ?>

        <div class="empty-history">

            <i class="fas fa-check-circle"></i>

            <p>

                No tienes comprobantes rechazados.

            </p>

        </div>

        <?php

        }

        ?>

        </div>
    <!-- CONFIRMADOS -->

    <div class="card-custom history-card">

        <h4>

            <i class="fas fa-check-circle"></i>

            Comprobantes Confirmados

        </h4>

        <?php

        if($confirmados->num_rows > 0){

            while($row = $confirmados->fetch_assoc()){

                ?>

                <div class="history-item">

                    <div>

                        <strong>

                            <?php

                            echo !empty($row['concepto'])

                            ?

                            $row['concepto']

                            :

                            'Pago confirmado';

                            ?>

                        </strong>

                        <small>

                            <?php

                            echo date(

                                'd/m/Y H:i',

                                strtotime(
                                    $row['fecha_subida']
                                )

                            );

                            ?>

                        </small>

                    </div>

                    <div class="history-actions">

                        <span class="status-confirmed">

                            Confirmado

                        </span>

                        <button
                            class="btn-view viewComprobanteBtn"
                            data-archivo="<?php echo $row['archivo']; ?>">

                            <i class="fas fa-eye"></i>

                            Ver

                        </button>

                    </div>

                </div>

                <?php

            }

        }else{

            ?>

            <div class="empty-history">

                <i class="fas fa-check"></i>

                <p>

                    Aún no tienes pagos confirmados.

                </p>

            </div>

            <?php

        }

        ?>

    </div>

</div>

<div class="modal fade" id="viewComprobanteModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4>

                    <i class="fas fa-image"></i>

                    Comprobante de Pago

                </h4>

                <button 
                type="button" 
                class="close" 
                data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body text-center">

                <img
                    id="previewImage"
                    class="img-fluid rounded d-none"
                    style="max-height:600px;">

                <iframe
                    id="previewPdf"
                    class="d-none"
                    style="width:100%; height:600px; border:none;">

                </iframe>

            </div>

            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn btn-cancel"
                    data-dismiss="modal">

                        Cerrar

                </button>

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php' ?>

<script src="js/transferencias_clientes.js"></script>

</body>
</html>