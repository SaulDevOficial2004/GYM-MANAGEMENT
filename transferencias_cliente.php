<?php
require_once __DIR__ . '/includes/signed_link.php';

$enlace = validarEnlaceCliente();

$folio = $enlace['folio'];
$expira = $enlace['expira'];
$firma = $enlace['firma'];

$sqlPersona="
    SELECT id, nombre, folio, fecha_fin
    FROM personas
    WHERE folio=?
    LIMIT 1
";

$stmt=$connect->prepare($sqlPersona);
$stmt->bind_param('s',$folio);
$stmt->execute();
$resultPersona=$stmt->get_result();

if($resultPersona->num_rows===0){
    header('Location: inicio.php');
    exit();
}

$persona=$resultPersona->fetch_assoc();

$fechaActual=new DateTime();
$fechaFin=new DateTime($persona['fecha_fin']);
$membresiaActiva=$fechaActual<=$fechaFin;

$sqlPendientes="
    SELECT id, concepto, fecha_subida, archivo, motivo_rechazo
    FROM comprobantes_pago
    WHERE persona_id=?
      AND status='PENDIENTE'
    ORDER BY fecha_subida DESC
";

$stmtPendientes=$connect->prepare($sqlPendientes);
$stmtPendientes->bind_param('i',$persona['id']);
$stmtPendientes->execute();
$pendientes=$stmtPendientes->get_result();

$sqlConfirmados="
    SELECT id, concepto, fecha_subida, archivo, motivo_rechazo
    FROM comprobantes_pago
    WHERE persona_id=?
      AND status='CONFIRMADO'
    ORDER BY fecha_subida DESC
";

$stmtConfirmados=$connect->prepare($sqlConfirmados);
$stmtConfirmados->bind_param('i',$persona['id']);
$stmtConfirmados->execute();
$confirmados=$stmtConfirmados->get_result();

$sqlRechazados="
    SELECT id, concepto, fecha_subida, archivo, motivo_rechazo
    FROM comprobantes_pago
    WHERE persona_id=?
      AND status='RECHAZADO'
    ORDER BY fecha_subida DESC
";

$stmtRechazados=$connect->prepare($sqlRechazados);
$stmtRechazados->bind_param('i',$persona['id']);
$stmtRechazados->execute();
$resultRechazados=$stmtRechazados->get_result();

$totalPendientes=$pendientes->num_rows;
$totalConfirmados=$confirmados->num_rows;
$totalRechazados=$resultRechazados->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>
        Transferencias de <?php echo htmlspecialchars($persona['nombre'],ENT_QUOTES,'UTF-8'); ?>
    </title>

    <?php include 'includes/head.php'; ?>

    <link rel="stylesheet" href="css/transferencia_clientes.css">
</head>
<body>

<div class="container page-container">

    <div class="welcome-card">
        <div class="welcome-content">
            <span class="welcome-label">
                Portal del cliente
            </span>

            <h2>
                <i class="fas fa-user-circle"></i>

                Hola,
                <?php echo htmlspecialchars($persona['nombre'],ENT_QUOTES,'UTF-8'); ?>
            </h2>

            <p>
                Consulta tu membresía y administra tus comprobantes de pago.
            </p>
        </div>

        <div class="folio-box">
            <span>Folio único</span>

            <strong>
                <?php echo htmlspecialchars($persona['folio'],ENT_QUOTES,'UTF-8'); ?>
            </strong>
        </div>
    </div>

    <div class="card-custom membership-status-card">
        <div class="status-header">
            <div class="status-title">
                <span>
                    <i class="fas fa-id-card"></i>
                </span>

                <div>
                    <small>Información actual</small>
                    <h4>Estado de membresía</h4>
                </div>
            </div>

            <?php if($membresiaActiva){ ?>
                <span class="status-active">
                    <i class="fas fa-check-circle"></i>
                    Activa
                </span>
            <?php }else{ ?>
                <span class="status-expired">
                    <i class="fas fa-times-circle"></i>
                    Vencida
                </span>
            <?php } ?>
        </div>

        <div class="status-info">
            <div class="status-info-item">
                <span>
                    <i class="fas fa-calendar-alt"></i>
                </span>

                <div>
                    <small>Fecha de vencimiento</small>

                    <h5>
                        <?php echo date('d/m/Y',strtotime($persona['fecha_fin'])); ?>
                    </h5>
                </div>
            </div>

            <div class="status-info-item">
                <span>
                    <i class="fas fa-shield-alt"></i>
                </span>

                <div>
                    <small>Estado actual</small>

                    <h5>
                        <?php echo $membresiaActiva?'Acceso vigente':'Renovación necesaria'; ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <?php if(!$membresiaActiva){ ?>

        <div class="card-custom upload-card">
            <div class="upload-header">
                <div class="upload-header-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>

                <div>
                    <span>Renovación de membresía</span>

                    <h4>Subir comprobante de pago</h4>

                    <p>
                        Adjunta el comprobante de tu transferencia.
                        El pago será revisado antes de activar tu membresía.
                    </p>
                </div>
            </div>

            <form id="uploadComprobanteForm" enctype="multipart/form-data">
                <input
                    type="hidden"
                    id="persona_id"
                    name="persona_id"
                    value="<?php echo (int)$persona['id']; ?>">

                <input
                    type="hidden"
                    id="folio_cliente"
                    name="folio_cliente"
                    value="<?php echo htmlspecialchars($persona['folio'],ENT_QUOTES,'UTF-8'); ?>">

                <input type="hidden" name="f" value="<?= htmlspecialchars($folio, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="e" value="<?= htmlspecialchars($expira, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="s" value="<?= htmlspecialchars($firma, ENT_QUOTES, 'UTF-8') ?>">

                <?php if (TURNSTILE_SITEKEY !== ''): ?>
                    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                    <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars(TURNSTILE_SITEKEY, ENT_QUOTES, 'UTF-8') ?>"></div>
                <?php endif; ?>

                <div class="upload-form-grid">
                    <div class="form-group">
                        <label for="concepto">
                            Concepto
                            <small>Opcional</small>
                        </label>

                        <input
                            type="text"
                            id="concepto"
                            name="concepto"
                            class="form-control modern-input"
                            placeholder="Ej. Pago mensualidad julio"
                            maxlength="150">
                    </div>

                    <div class="form-group">
                        <label for="archivo">
                            Comprobante
                        </label>

                        <input
                            type="file"
                            id="archivo"
                            name="archivo"
                            class="form-control modern-input"
                            accept=".jpg,.jpeg,.png,.pdf"
                            required>

                        <small class="file-help">
                            Formatos permitidos: JPG, PNG o PDF.
                        </small>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn-upload"
                    id="uploadComprobanteButton">

                    <i class="fas fa-paper-plane"></i>

                    <span>Enviar comprobante</span>
                </button>
            </form>
        </div>

    <?php }else{ ?>

        <div class="membership-active-card">
            <div class="membership-active-icon">
                <i class="fas fa-check"></i>
            </div>

            <div class="membership-active-content">
                <span class="membership-active-label">
                    Membresía vigente
                </span>

                <h3>Tu membresía está activa</h3>

                <p>
                    No necesitas subir un comprobante mientras tu membresía continúe vigente.
                </p>
            </div>

            <div class="membership-active-date">
                <small>Válida hasta</small>

                <strong>
                    <?php echo date('d/m/Y',strtotime($persona['fecha_fin'])); ?>
                </strong>
            </div>
        </div>

    <?php } ?>

    <section class="payment-history-section">
        <div class="payment-history-header">
            <div>
                <span class="payment-history-label">
                    Historial de pagos
                </span>

                <h3>Seguimiento de comprobantes</h3>

                <p>
                    Consulta los pagos enviados y el resultado de cada revisión.
                </p>
            </div>

            <div class="payment-history-summary">
                <span class="summary-pending">
                    <?php echo $totalPendientes; ?>
                    pendientes
                </span>

                <span class="summary-rejected">
                    <?php echo $totalRechazados; ?>
                    rechazados
                </span>

                <span class="summary-confirmed">
                    <?php echo $totalConfirmados; ?>
                    confirmados
                </span>
            </div>
        </div>

        <div class="history-grid">

            <article class="card-custom history-card history-pending">
                <div class="history-card-header">
                    <div class="history-card-title">
                        <span>
                            <i class="fas fa-clock"></i>
                        </span>

                        <div>
                            <small>En proceso</small>
                            <h4>Comprobantes pendientes</h4>
                        </div>
                    </div>

                    <span class="history-count">
                        <?php echo $totalPendientes; ?>
                    </span>
                </div>

                <div class="history-card-content">
                    <?php if($totalPendientes>0){ ?>

                        <?php while($row=$pendientes->fetch_assoc()){ ?>

                            <div class="history-item">
                                <div class="history-item-main">
                                    <strong>
                                        <?php
                                        echo !empty($row['concepto'])
                                            ?htmlspecialchars($row['concepto'],ENT_QUOTES,'UTF-8')
                                            :'Sin concepto';
                                        ?>
                                    </strong>

                                    <small>
                                        <i class="fas fa-calendar-alt"></i>

                                        <?php
                                        echo date(
                                            'd/m/Y H:i',
                                            strtotime($row['fecha_subida'])
                                        );
                                        ?>
                                    </small>
                                </div>

                                <div class="history-actions">
                                    <span class="status-pending">
                                        En revisión
                                    </span>

                                    <button
                                        type="button"
                                        class="btn-view viewComprobanteBtn"
                                        data-id="<?php echo (int)$row['id']; ?>"
                                        data-archivo="<?php echo htmlspecialchars($row['archivo'],ENT_QUOTES,'UTF-8'); ?>">

                                        <i class="fas fa-eye"></i>
                                        Ver
                                    </button>
                                </div>
                            </div>

                        <?php } ?>

                    <?php }else{ ?>

                        <div class="empty-history">
                            <span>
                                <i class="fas fa-inbox"></i>
                            </span>

                            <h5>Sin comprobantes pendientes</h5>

                            <p>
                                No tienes pagos esperando revisión.
                            </p>
                        </div>

                    <?php } ?>
                </div>
            </article>

            <article class="card-custom history-card history-rejected">
                <div class="history-card-header">
                    <div class="history-card-title">
                        <span>
                            <i class="fas fa-times-circle"></i>
                        </span>

                        <div>
                            <small>Requieren atención</small>
                            <h4>Comprobantes rechazados</h4>
                        </div>
                    </div>

                    <span class="history-count">
                        <?php echo $totalRechazados; ?>
                    </span>
                </div>

                <div class="history-card-content">
                    <?php if($totalRechazados>0){ ?>

                        <?php while($row=$resultRechazados->fetch_assoc()){ ?>

                            <div class="history-item history-item-rejected">
                                <div class="history-item-main">
                                    <strong>
                                        <?php
                                        echo !empty($row['concepto'])
                                            ?htmlspecialchars($row['concepto'],ENT_QUOTES,'UTF-8')
                                            :'Sin concepto';
                                        ?>
                                    </strong>

                                    <small>
                                        <i class="fas fa-calendar-alt"></i>

                                        <?php
                                        echo date(
                                            'd/m/Y H:i',
                                            strtotime($row['fecha_subida'])
                                        );
                                        ?>
                                    </small>

                                    <div class="rejection-reason">
                                        <span>Motivo del rechazo</span>

                                        <p>
                                            <?php
                                            echo nl2br(
                                                htmlspecialchars(
                                                    $row['motivo_rechazo']??'Sin motivo registrado',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                            );
                                            ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="history-actions">
                                    <span class="status-rejected">
                                        Rechazado
                                    </span>

                                    <button
                                        type="button"
                                        class="btn-view viewComprobanteBtn"
                                        data-id="<?php echo (int)$row['id']; ?>"
                                        data-archivo="<?php echo htmlspecialchars($row['archivo'],ENT_QUOTES,'UTF-8'); ?>">

                                        <i class="fas fa-eye"></i>
                                        Ver
                                    </button>
                                </div>
                            </div>

                        <?php } ?>

                    <?php }else{ ?>

                        <div class="empty-history">
                            <span>
                                <i class="fas fa-check-circle"></i>
                            </span>

                            <h5>Sin comprobantes rechazados</h5>

                            <p>
                                No tienes pagos que requieran corrección.
                            </p>
                        </div>

                    <?php } ?>
                </div>
            </article>

            <article class="card-custom history-card history-confirmed">
                <div class="history-card-header">
                    <div class="history-card-title">
                        <span>
                            <i class="fas fa-check-circle"></i>
                        </span>

                        <div>
                            <small>Pagos aprobados</small>
                            <h4>Comprobantes confirmados</h4>
                        </div>
                    </div>

                    <span class="history-count">
                        <?php echo $totalConfirmados; ?>
                    </span>
                </div>

                <div class="history-card-content">
                    <?php if($totalConfirmados>0){ ?>

                        <?php while($row=$confirmados->fetch_assoc()){ ?>

                            <div class="history-item">
                                <div class="history-item-main">
                                    <strong>
                                        <?php
                                        echo !empty($row['concepto'])
                                            ?htmlspecialchars($row['concepto'],ENT_QUOTES,'UTF-8')
                                            :'Pago confirmado';
                                        ?>
                                    </strong>

                                    <small>
                                        <i class="fas fa-calendar-alt"></i>

                                        <?php
                                        echo date(
                                            'd/m/Y H:i',
                                            strtotime($row['fecha_subida'])
                                        );
                                        ?>
                                    </small>
                                </div>

                                <div class="history-actions">
                                    <span class="status-confirmed">
                                        Confirmado
                                    </span>

                                    <button
                                        type="button"
                                        class="btn-view viewComprobanteBtn"
                                        data-id="<?php echo (int)$row['id']; ?>"
                                        data-archivo="<?php echo htmlspecialchars($row['archivo'],ENT_QUOTES,'UTF-8'); ?>">

                                        <i class="fas fa-eye"></i>
                                        Ver
                                    </button>
                                </div>
                            </div>

                        <?php } ?>

                    <?php }else{ ?>

                        <div class="empty-history">
                            <span>
                                <i class="fas fa-receipt"></i>
                            </span>

                            <h5>Sin pagos confirmados</h5>

                            <p>
                                Los comprobantes aprobados aparecerán aquí.
                            </p>
                        </div>

                    <?php } ?>
                </div>
            </article>

        </div>
    </section>

</div>

<div class="modal fade" id="viewComprobanteModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4>
                    <i class="fas fa-file-invoice"></i>
                    Comprobante de pago
                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Cerrar">

                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <img
                    id="previewImage"
                    class="img-fluid rounded d-none"
                    alt="Comprobante de pago">

                <iframe
                    id="previewPdf"
                    class="d-none"
                    title="Comprobante de pago en PDF">
                </iframe>
            </div>

            <div class="modal-footer border-0">
                <button
                    type="button"
                    class="btn-cancel"
                    data-dismiss="modal">

                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JS -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.js"
        integrity="sha384-VwoO4KYHycI5E2Vzjf4m+IY3C8JKnLhRzzVeR7n4Qdx+Qkq0YUC3aiJ6vY0XVlVT"
        crossorigin="anonymous"></script>

<script src="js/transferencias_clientes.js"></script>

</body>
</html>
