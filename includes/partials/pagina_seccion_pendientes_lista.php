    <section id="pendingPaymentsContainer" class="pending-payments-section" style="display:none;">
        <div class="section-heading">
            <div>
                <h2>Comprobantes por revisar</h2>
                <p>Selecciona un comprobante para consultar sus detalles.</p>
            </div>
        </div>

        <div class="pending-grid">
            <?php if ($totalPendientes > 0): ?>
                <?php foreach (
                    $comprobantesPendientes as $row
                ): ?>
                    <article class="payment-card">
                        <div class="payment-top">
                            <div class="payment-client">
                                <span>
                                    <i class="fas fa-user"></i>
                                </span>
                                <div>
                                    <h4>
                                        <?php echo htmlspecialchars(
                                            $row['nombre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </h4>
                                    <small>
                                        <?php echo htmlspecialchars(
                                            $row['folio_cliente'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </small>
                                </div>
                            </div>
                            <span class="status-pending">Pendiente</span>
                        </div>

                        <div class="payment-body">
                            <div>
                                <small>Membresía</small>
                                <strong>
                                    <?php echo htmlspecialchars(
                                        $row['membresia'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </strong>
                            </div>
                            <div>
                                <small>Concepto</small>
                                <strong>
                                    <?php echo !empty(
                                        $row['concepto']
                                    )
                                        ? htmlspecialchars(
                                            $row['concepto'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                        : 'Sin concepto'; ?>
                                </strong>
                            </div>
                            <div>
                                <small>Fecha</small>
                                <strong>
                                    <?php echo date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $row['fecha_subida']
                                        )
                                    ); ?>
                                </strong>
                            </div>
                        </div>

                        <button type="button" class="view-payment-button viewPaymentBtn"
                            data-id="<?php echo $row['id']; ?>"
                            data-persona="<?php echo $row['persona_id']; ?>"
                            data-nombre="<?php echo htmlspecialchars(
                                $row['nombre'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            data-folio="<?php echo htmlspecialchars(
                                $row['folio_cliente'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            data-membresia="<?php echo htmlspecialchars(
                                $row['membresia'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            data-dias="<?php echo $row['dias']; ?>"
                            data-concepto="<?php echo htmlspecialchars(
                                $row['concepto'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            data-status="<?php echo htmlspecialchars(
                                $row['status'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            data-fecha="<?php echo htmlspecialchars(
                                $row['fecha_subida'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            data-archivo="<?php echo htmlspecialchars(
                                $row['archivo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>">
                            <i class="fas fa-eye"></i>
                            Ver comprobante
                        </button>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="dashboard-empty-state">
                    <i class="fas fa-circle-check"></i>
                    <h3>No hay comprobantes pendientes</h3>
                    <p>Todos los pagos han sido revisados.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
