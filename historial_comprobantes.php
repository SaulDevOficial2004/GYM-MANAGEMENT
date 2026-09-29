<?php

require_once __DIR__ . '/includes/auth.php';

requireRoles([
    'Administrador',
    'Dueño'
]);

$nombre = $_SESSION['nombre'];
?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Historial de comprobantes</title>

    <?php include 'includes/head.php' ?>

    <link
        rel="stylesheet"
        href="css/historial_comprobantes.css">

</head>

<body>

<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<main class="payment-history-container">
    <header class="payment-history-hero">
        <div class="payment-history-heading">
            <span class="payment-history-heading-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </span>
            <div>
                <p class="payment-history-eyebrow">Control de pagos</p>
                <h1>Historial de comprobantes</h1>
                <p>Consulta, filtra y revisa todos los comprobantes enviados por los clientes.</p>
            </div>
        </div>
        <a href="pagina.php" class="payment-history-back">
            <i class="fas fa-arrow-left"></i>
            Volver al dashboard
        </a>
    </header>

    <section class="payment-history-stats">
        <article class="payment-stat-card stat-total">
            <span class="payment-stat-icon">
                <i class="fas fa-receipt"></i>
            </span>
            <div>
                <small>Total</small>
                <strong id="paymentHistoryTotal">0</strong>
                <span>Comprobantes registrados</span>
            </div>
        </article>

        <article class="payment-stat-card stat-pending">
            <span class="payment-stat-icon">
                <i class="fas fa-clock"></i>
            </span>
            <div>
                <small>Pendientes</small>
                <strong id="paymentHistoryPending">0</strong>
                <span>Esperando revisión</span>
            </div>
        </article>

        <article class="payment-stat-card stat-confirmed">
            <span class="payment-stat-icon">
                <i class="fas fa-circle-check"></i>
            </span>
            <div>
                <small>Confirmados</small>
                <strong id="paymentHistoryConfirmed">0</strong>
                <span>Pagos aprobados</span>
            </div>
        </article>

        <article class="payment-stat-card stat-rejected">
            <span class="payment-stat-icon">
                <i class="fas fa-circle-xmark"></i>
            </span>
            <div>
                <small>Rechazados</small>
                <strong id="paymentHistoryRejected">0</strong>
                <span>Pagos no aprobados</span>
            </div>
        </article>
    </section>

    <section class="payment-history-card">
        <div class="payment-history-card-header">
            <div class="payment-history-card-title">
                <span>
                    <i class="fas fa-list"></i>
                </span>
                <div>
                    <p class="payment-history-eyebrow">Registro histórico</p>
                    <h2>Comprobantes enviados</h2>
                    <p>Se muestran como máximo 20 registros por página.</p>
                </div>
            </div>
            <div class="payment-history-result">
                <strong id="paymentHistoryRecords">0</strong>
                <small>resultados encontrados</small>
            </div>
        </div>

        <div class="payment-history-toolbar">
            <div class="payment-history-search">
                <i class="fas fa-search"></i>
                <input type="text" id="paymentHistorySearch" placeholder="Buscar cliente, folio, concepto, fecha o responsable" autocomplete="off">
                <button type="button" id="clearPaymentHistorySearch" title="Limpiar búsqueda">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="payment-history-filters">
                <button type="button" class="payment-history-filter active" data-status="all">
                    <i class="fas fa-layer-group"></i>
                    Todos
                </button>
                <button type="button" class="payment-history-filter" data-status="PENDIENTE">
                    <i class="fas fa-clock"></i>
                    Pendientes
                </button>
                <button type="button" class="payment-history-filter" data-status="CONFIRMADO">
                    <i class="fas fa-check"></i>
                    Confirmados
                </button>
                <button type="button" class="payment-history-filter" data-status="RECHAZADO">
                    <i class="fas fa-times"></i>
                    Rechazados
                </button>
            </div>
        </div>

        <div class="payment-history-table-container">
            <div class="table-responsive">
                <table class="table payment-history-table" id="paymentHistoryTable">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Folio</th>
                            <th>Concepto</th>
                            <th>Estado</th>
                            <th>Fecha de envío</th>
                            <th>Revisó</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="paymentHistoryTableBody">
                        <tr>
                            <td colspan="7" class="payment-history-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Cargando comprobantes...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="payment-history-empty d-none" id="paymentHistoryEmpty">
                <span>
                    <i class="fas fa-file-circle-xmark"></i>
                </span>
                <h3>No se encontraron comprobantes</h3>
                <p>Prueba con otra búsqueda o selecciona un estado diferente.</p>
            </div>
        </div>

        <div class="payment-history-pagination">
            <button type="button" id="paymentHistoryPrevious" class="payment-history-page-button" disabled>
                <i class="fas fa-chevron-left"></i>
                Anterior
            </button>
            <span id="paymentHistoryPageInformation">Página 1 de 1</span>
            <button type="button" id="paymentHistoryNext" class="payment-history-page-button" disabled>
                Siguiente
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>
</main>

<!-- MODAL DE DETALLE -->

<div
    class="modal fade"
    id="paymentHistoryViewModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">

    <div
        class="modal-dialog modal-xl modal-dialog-centered"
        role="document">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h3>

                    <i class="fas fa-receipt"></i>

                    Detalle del comprobante

                </h3>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Cerrar">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-lg-5 mb-4 mb-lg-0">

                        <div class="history-info-card">

                            <div class="history-info-item">

                                <strong>Cliente</strong>

                                <span id="historyNombre"></span>

                            </div>

                            <div class="history-info-item">

                                <strong>Folio</strong>

                                <span id="historyFolio"></span>

                            </div>

                            <div class="history-info-item">

                                <strong>Concepto</strong>

                                <span id="historyConcepto"></span>

                            </div>

                            <div class="history-info-item">

                                <strong>Fecha de envío</strong>

                                <span id="historyFecha"></span>

                            </div>

                            <div class="history-info-item">

                                <strong>Estado</strong>

                                <span id="historyEstado"></span>

                            </div>

                            <div class="history-info-item">

                                <strong>Revisado por</strong>

                                <span id="historyRevisado"></span>

                            </div>

                            <div class="history-info-item">

                                <strong>Fecha de revisión</strong>

                                <span id="historyFechaRevision"></span>

                            </div>

                            <div
                                id="historyMotivoContainer"
                                class="d-none">

                                <strong>Motivo del rechazo</strong>

                                <div id="historyMotivo"></div>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-7">

                        <div class="history-preview">

                            <img
                                id="historyPreviewImage"
                                class="d-none"
                                alt="Comprobante de pago">

                            <iframe
                                id="historyPreviewPdf"
                                class="d-none"
                                title="Comprobante PDF">
                            </iframe>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn-cancel"
                    data-dismiss="modal">

                    Cerrar

                </button>

            </div>

        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>

<script src="js/historial_comprobantes.js"></script>

</body>

</html>