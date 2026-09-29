        <article class="dashboard-panel pending-panel <?php echo $cardColor; ?>">
            <div class="panel-header">
                <div>
                    <h2>Comprobantes pendientes</h2>
                    <p>Pagos esperando revisión</p>
                </div>
                <span class="pending-counter">
                    <?php echo intval(
                        $totalPendientes
                    ); ?>
                </span>
            </div>

            <div class="pending-summary">
                <span class="pending-summary-icon">
                    <i class="fas fa-file-circle-check"></i>
                </span>
                <div>
                    <strong>
                        <?php echo $totalPendientes > 0
                            ? 'Revisión necesaria'
                            : 'Todo al día'; ?>
                    </strong>
                    <small>
                        <?php echo $totalPendientes > 0
                            ? 'Hay comprobantes esperando una decisión.'
                            : 'No hay comprobantes pendientes.'; ?>
                    </small>
                </div>
            </div>

            <div class="pending-actions">
                <button type="button" class="panel-action-primary" id="showPendingPayments">
                    <i class="fas fa-eye"></i>
                    <span>Ver comprobantes</span>
                </button>

                <?php if (
                    isRole('Administrador')
                    || isRole('Dueño')
                ): ?>
                    <a href="historial_comprobantes.php" class="panel-action-secondary">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span>Historial</span>
                    </a>

                    <button type="button" class="panel-action-secondary" data-toggle="modal" data-target="#transferSettingsModal">
                        <i class="fas fa-gear"></i>
                        <span>Cuenta bancaria</span>
                    </button>
                <?php endif; ?>
            </div>
        </article>
