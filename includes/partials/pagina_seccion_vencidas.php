    <section class="dashboard-panel expired-panel">
        <div class="panel-header">
            <div>
                <h2>Membresías vencidas</h2>
                <p>Clientes que necesitan renovar su membresía</p>
            </div>
            <span class="expired-counter">
                <?php echo $totalVencidos; ?>
            </span>
        </div>

        <div class="expired-desktop-table">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalVencidos > 0): ?>
                            <?php foreach (
                                $miembrosVencidos as $miembro
                            ): ?>
                                <?php
                                $fechaFin = date(
                                    'd/m/Y',
                                    strtotime(
                                        $miembro['fecha_fin']
                                    )
                                );
                                ?>
                                <tr>
                                    <td>
                                        <div class="expired-client">
                                            <span>
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <strong>
                                                <?php echo htmlspecialchars(
                                                    $miembro['nombre'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </strong>
                                        </div>
                                    </td>
                                    <td><?php echo $fechaFin; ?></td>
                                    <td>
                                        <span class="status-expired">Vencida</span>
                                    </td>
                                    <td>
                                        <div class="expired-actions">
                                            <button type="button" class="btn-update updateBtn"
                                                data-toggle="modal"
                                                data-target="#updateMembershipModal"
                                                data-id="<?php echo $miembro['id']; ?>"
                                                data-nombre="<?php echo htmlspecialchars(
                                                    $miembro['nombre'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>">
                                                <i class="fas fa-credit-card"></i>
                                                Renovar
                                            </button>

                                            <button type="button" class="btn-delete disableMemberBtn"
                                                data-id="<?php echo $miembro['id']; ?>"
                                                title="Inhabilitar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    No hay membresías vencidas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="expired-mobile-list">
            <?php if ($totalVencidos > 0): ?>
                <?php foreach (
                    $miembrosVencidos as $miembro
                ): ?>
                    <?php
                    $fechaFin = date(
                        'd/m/Y',
                        strtotime(
                            $miembro['fecha_fin']
                        )
                    );
                    ?>
                    <article class="expired-mobile-card">
                        <div class="expired-mobile-info">
                            <span>
                                <i class="fas fa-user"></i>
                            </span>
                            <div>
                                <strong>
                                    <?php echo htmlspecialchars(
                                        $miembro['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </strong>
                                <small>
                                    Venció el <?php echo $fechaFin; ?>
                                </small>
                            </div>
                        </div>

                        <div class="expired-mobile-actions">
                            <button type="button" class="updateBtn"
                                data-toggle="modal"
                                data-target="#updateMembershipModal"
                                data-id="<?php echo $miembro['id']; ?>"
                                data-nombre="<?php echo htmlspecialchars(
                                    $miembro['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                title="Renovar">
                                <i class="fas fa-credit-card"></i>
                            </button>

                            <button type="button" class="disableMemberBtn"
                                data-id="<?php echo $miembro['id']; ?>"
                                title="Inhabilitar">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="dashboard-empty-state compact">
                    <i class="fas fa-circle-check"></i>
                    <p>No hay membresías vencidas.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
