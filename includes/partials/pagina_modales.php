<!-- MODAL ACTUALIZAR MEMBRESÍA -->
<div class="modal fade" id="updateMembershipModal" tabindex="-1" role="dialog" aria-labelledby="updateMembershipTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-primary">
                        <i class="fas fa-credit-card"></i>
                    </span>
                    <div>
                        <h4 id="updateMembershipTitle">Actualizar membresía</h4>
                        <p>Selecciona el nuevo plan y verifica las fechas.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="updateMembershipForm" method="POST">
                <div class="modal-body dashboard-modal-body">
                    <input type="hidden" name="id" id="cliente_id">
                    <div class="modal-client-summary">
                        <span class="modal-client-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div>
                            <small>Cliente seleccionado</small>
                            <strong id="cliente_nombre"></strong>
                        </div>
                    </div>
                    <div class="form-group dashboard-form-group">
                        <label for="persona_membresia_edit">Membresía</label>
                        <select id="persona_membresia_edit" class="form-control dashboard-input" required>
                            <option value="">Seleccione una membresía</option>
                            <?php foreach ($membresiasActivas as $membresia): ?>
                                <option value="<?php echo $membresia['id']; ?>" data-dias="<?php echo $membresia['dias']; ?>">
                                    <?php echo htmlspecialchars(
                                        $membresia['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="dashboard-form-grid">
                        <div class="form-group dashboard-form-group">
                            <label for="fecha_ini">Fecha inicial</label>
                            <input type="date" name="fecha_ini" id="fecha_ini" class="form-control dashboard-input" required>
                        </div>
                        <div class="form-group dashboard-form-group">
                            <label for="fecha_fin">Fecha de vencimiento</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control dashboard-input" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer dashboard-modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-button modal-button-primary">
                        <i class="fas fa-check"></i>
                        Guardar membresía
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL AGREGAR PERSONA -->
<?php include 'includes/modals/add_person_modal.php'; ?>

<!-- MODAL NUEVA VISITA -->
<div class="modal fade" id="newVisitModal" tabindex="-1" role="dialog" aria-labelledby="newVisitTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-success">
                        <i class="fas fa-user-plus"></i>
                    </span>
                    <div>
                        <h4 id="newVisitTitle">Nueva visita</h4>
                        <p>Busca un visitante para registrar su entrada.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body dashboard-modal-body">
                <div class="form-group dashboard-form-group">
                    <label for="searchVisitante">Buscar visitante</label>
                    <div class="dashboard-search-field">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchVisitante" class="form-control dashboard-input" placeholder="Escriba el nombre del visitante" autocomplete="off">
                    </div>
                </div>
                <div id="visitantesResults" class="modal-results-container"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CREAR VISITANTE -->
<div class="modal fade" id="createVisitanteModal" tabindex="-1" role="dialog" aria-labelledby="createVisitanteTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-success">
                        <i class="fas fa-user-check"></i>
                    </span>
                    <div>
                        <h4 id="createVisitanteTitle">Crear visitante</h4>
                        <p>Registra a una persona que aún no existe.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createVisitanteForm">
                <div class="modal-body dashboard-modal-body">
                    <div class="form-group dashboard-form-group">
                        <label for="visitante_nombre">Nombre completo</label>
                        <input type="text" id="visitante_nombre" class="form-control dashboard-input" placeholder="Ingrese el nombre del visitante" autocomplete="off" required>
                    </div>
                </div>
                <div class="modal-footer dashboard-modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-button modal-button-primary">
                        <i class="fas fa-user-plus"></i>
                        Guardar visitante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAR VENTA DE PRODUCTO -->
<div class="modal fade" id="sellProductQuantityModal" tabindex="-1" role="dialog" aria-labelledby="sellProductQuantityTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-primary">
                        <i class="fas fa-cart-shopping"></i>
                    </span>
                    <div>
                        <h4 id="sellProductQuantityTitle">Confirmar venta</h4>
                        <p>Verifica el producto y la cantidad solicitada.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="sellProductDashboardForm">
                <input type="hidden" id="dashboard_producto_id">
                <div class="modal-body dashboard-modal-body">
                    <div class="dashboard-product-summary">
                        <span class="dashboard-product-icon">
                            <i class="fas fa-box-open"></i>
                        </span>
                        <div>
                            <small>Producto seleccionado</small>
                            <strong id="dashboard_producto_nombre_display">Confirma la información</strong>
                        </div>
                    </div>
                    <div class="form-group dashboard-form-group">
                        <label for="dashboard_producto_nombre">Producto</label>
                        <input type="text" id="dashboard_producto_nombre" class="form-control dashboard-input dashboard-input-readonly" readonly>
                    </div>
                    <div class="dashboard-form-grid">
                        <div class="form-group dashboard-form-group">
                            <label for="dashboard_producto_stock">Stock disponible</label>
                            <input type="text" id="dashboard_producto_stock" class="form-control dashboard-input dashboard-input-readonly" readonly>
                        </div>
                        <div class="form-group dashboard-form-group">
                            <label for="dashboard_producto_cantidad">Cantidad</label>
                            <input type="number" id="dashboard_producto_cantidad" min="1" value="1" class="form-control dashboard-input" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer dashboard-modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-button modal-button-primary">
                        <i class="fas fa-check"></i>
                        Confirmar venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BUSCAR PRODUCTO -->
<div class="modal fade" id="sellProductDashboardModal" tabindex="-1" role="dialog" aria-labelledby="sellProductDashboardTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-primary">
                        <i class="fas fa-box-open"></i>
                    </span>
                    <div>
                        <h4 id="sellProductDashboardTitle">Vender producto</h4>
                        <p>Busca un producto disponible en el inventario.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body dashboard-modal-body">
                <div class="form-group dashboard-form-group">
                    <label for="searchProductDashboard">Buscar producto</label>
                    <div class="dashboard-search-field">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchProductDashboard" class="form-control dashboard-input" placeholder="Escriba el nombre del producto" autocomplete="off">
                    </div>
                </div>
                <div id="productsResults" class="modal-results-container"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL RENTAR TOALLA -->
<div class="modal fade" id="rentTowelModal" tabindex="-1" role="dialog" aria-labelledby="rentTowelTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-purple">
                        <i class="fas fa-shirt"></i>
                    </span>
                    <div>
                        <h4 id="rentTowelTitle">Rentar toalla</h4>
                        <p>Confirma el registro de una nueva renta.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="rentTowelForm">
                <div class="modal-body dashboard-modal-body">
                    <div class="modal-price-card">
                        <span class="modal-price-icon">
                            <i class="fas fa-shirt"></i>
                        </span>
                        <div>
                            <small>Precio de renta</small>
                            <strong>$25.00</strong>
                        </div>
                        <span class="modal-price-status">Disponible</span>
                    </div>
                    <p class="modal-helper-text">
                        La renta se registrará dentro de las ventas correspondientes al día actual.
                    </p>
                </div>
                <div class="modal-footer dashboard-modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-button modal-button-primary">
                        <i class="fas fa-check"></i>
                        Registrar renta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL REVISAR COMPROBANTE -->
<div class="modal fade" id="paymentReviewModal" tabindex="-1" role="dialog" aria-labelledby="paymentReviewTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal dashboard-modal-large">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-warning">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>
                    <div>
                        <h4 id="paymentReviewTitle">Revisión de comprobante</h4>
                        <p>Comprueba la información antes de confirmar el pago.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="hidden" id="reviewComprobanteId">
            <div class="modal-body dashboard-modal-body">
                <div class="payment-review-grid">
                    <div class="payment-preview-panel">
                        <div class="payment-preview-heading">
                            <span>Archivo adjunto</span>
                            <small>Vista previa del comprobante</small>
                        </div>
                        <div class="payment-preview-content">
                            <img id="adminPreviewImage" class="img-fluid d-none" alt="Comprobante de pago">
                            <iframe id="adminPreviewPdf" class="d-none" title="Comprobante de pago PDF"></iframe>
                            <div class="payment-preview-placeholder">
                                <i class="fas fa-file-image"></i>
                                <span>El archivo aparecerá aquí</span>
                            </div>
                        </div>
                    </div>
                    <div class="payment-information-panel">
                        <div class="payment-person-header">
                            <span>
                                <i class="fas fa-user"></i>
                            </span>
                            <div>
                                <small>Cliente</small>
                                <h4 id="reviewNombre"></h4>
                            </div>
                        </div>
                        <div class="payment-data-list">
                            <div class="payment-data-item">
                                <small>Folio</small>
                                <strong id="reviewFolio"></strong>
                            </div>
                            <div class="payment-data-item payment-data-item-full">
                                <label for="reviewMembership">Membresía</label>
                                <select id="reviewMembership" class="form-control dashboard-input">
                                    <?php
                                    foreach (
                                        $membresiasActivas as $m
                                    ) {
                                        $precio = $m['precio'];

                                        if (
                                            $m['promocion'] == 1
                                            && !empty($m['precio_promocion'])
                                        ) {
                                            $precio = $m['precio_promocion'];
                                        }
                                        ?>
                                        <option value="<?php echo $m['id']; ?>">
                                            <?php echo htmlspecialchars(
                                                $m['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) . ' - $' . number_format(
                                                $precio,
                                                2
                                            ); ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="payment-data-item">
                                <small>Concepto</small>
                                <strong id="reviewConcepto"></strong>
                            </div>
                            <div class="payment-data-item">
                                <small>Fecha</small>
                                <strong id="reviewFecha"></strong>
                            </div>
                            <div class="payment-data-item">
                                <small>Estado</small>
                                <span id="reviewStatus" class="status-pending">Pendiente</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer dashboard-modal-footer dashboard-modal-footer-spread">
                <button type="button" class="modal-button modal-button-secondary" data-dismiss="modal">
                    Cerrar
                </button>
                <div class="modal-footer-actions">
                    <button type="button" id="rejectPaymentBtn" class="modal-button modal-button-danger">
                        <i class="fas fa-times"></i>
                        Rechazar
                    </button>
                    <button type="button" id="confirmPaymentBtn" class="modal-button modal-button-primary">
                        <i class="fas fa-check"></i>
                        Confirmar pago
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HISTORIAL DE COMPROBANTE -->
<div class="modal fade" id="paymentHistoryViewModal" tabindex="-1" role="dialog" aria-labelledby="paymentHistoryTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal dashboard-modal-large">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-primary">
                        <i class="fas fa-file-lines"></i>
                    </span>
                    <div>
                        <h4 id="paymentHistoryTitle">Detalle del comprobante</h4>
                        <p>Consulta la información y el archivo del pago.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body dashboard-modal-body">
                <div class="payment-review-grid">
                    <div class="payment-preview-panel">
                        <div class="payment-preview-heading">
                            <span>Archivo adjunto</span>
                            <small>Vista previa del comprobante</small>
                        </div>
                        <div class="payment-preview-content">
                            <img id="historyPreviewImage" class="img-fluid d-none" alt="Comprobante de pago">
                            <iframe id="historyPreviewPdf" class="d-none" title="Historial de comprobante PDF"></iframe>
                            <div class="payment-preview-placeholder">
                                <i class="fas fa-file-image"></i>
                                <span>El archivo aparecerá aquí</span>
                            </div>
                        </div>
                    </div>
                    <div class="payment-information-panel">
                        <div class="payment-person-header">
                            <span>
                                <i class="fas fa-user"></i>
                            </span>
                            <div>
                                <small>Cliente</small>
                                <h4 id="historyNombre"></h4>
                            </div>
                        </div>
                        <div class="payment-data-list">
                            <div class="payment-data-item">
                                <small>Folio</small>
                                <strong id="historyFolio"></strong>
                            </div>
                            <div class="payment-data-item">
                                <small>Concepto</small>
                                <strong id="historyConcepto"></strong>
                            </div>
                            <div class="payment-data-item">
                                <small>Fecha</small>
                                <strong id="historyFecha"></strong>
                            </div>
                            <div class="payment-data-item">
                                <small>Estado</small>
                                <span id="historyEstado"></span>
                            </div>
                            <div id="historyMotivoContainer" class="payment-data-item payment-data-item-full d-none">
                                <small>Motivo</small>
                                <strong id="historyMotivo"></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer dashboard-modal-footer">
                <button type="button" class="modal-button modal-button-secondary" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CONFIGURACIÓN BANCARIA -->
<div class="modal fade" id="transferSettingsModal" tabindex="-1" role="dialog" aria-labelledby="transferSettingsTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dashboard-modal">
            <div class="modal-header dashboard-modal-header">
                <div class="dashboard-modal-heading">
                    <span class="dashboard-modal-icon modal-icon-primary">
                        <i class="fas fa-building-columns"></i>
                    </span>
                    <div>
                        <h4 id="transferSettingsTitle">Configuración bancaria</h4>
                        <p>Actualiza los datos mostrados para transferencias.</p>
                    </div>
                </div>
                <button type="button" class="dashboard-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="transferSettingsForm">
                <input type="hidden" id="transfer_config_id" value="<?php echo $configuracionTransferencias['id']; ?>">
                <div class="modal-body dashboard-modal-body">
                    <div class="modal-security-notice">
                        <i class="fas fa-shield-halved"></i>
                        <p>Verifica cuidadosamente los datos antes de guardarlos.</p>
                    </div>
                    <div class="form-group dashboard-form-group">
                        <label for="transfer_banco">Banco</label>
                        <input type="text" id="transfer_banco" class="form-control dashboard-input" value="<?php echo htmlspecialchars(
                            $configuracionTransferencias['banco'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group dashboard-form-group">
                        <label for="transfer_titular">Titular de la cuenta</label>
                        <input type="text" id="transfer_titular" class="form-control dashboard-input" value="<?php echo htmlspecialchars(
                            $configuracionTransferencias['titular'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" required>
                    </div>
                    <div class="form-group dashboard-form-group">
                        <label for="transfer_clabe">CLABE</label>
                        <input type="text" id="transfer_clabe" class="form-control dashboard-input" value="<?php echo htmlspecialchars(
                            $configuracionTransferencias['clabe'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>" inputmode="numeric" maxlength="18" required>
                    </div>
                </div>
                <div class="modal-footer dashboard-modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="modal-button modal-button-primary">
                        <i class="fas fa-floppy-disk"></i>
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
