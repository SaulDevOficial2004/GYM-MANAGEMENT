<?php

if (!isset($connect)) {
    require_once __DIR__ . '/../../php_action/conn_db.php';
}

$sqlMembresias = "
    SELECT *
    FROM membresias
    WHERE activo = 1
    ORDER BY dias ASC
";

$resultMembresias = $connect->query(
    $sqlMembresias
);

?>

<div class="modal fade" id="addPersonModal" tabindex="-1" role="dialog" aria-labelledby="addPersonTitle" aria-hidden="true">
    <div class="modal-dialog add-person-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content add-person-modal">
            <div class="modal-header add-person-modal-header">
                <div class="add-person-modal-heading">
                    <span class="add-person-modal-icon">
                        <i class="fas fa-user-plus"></i>
                    </span>
                    <div>
                        <h4 id="addPersonTitle">Registrar persona</h4>
                        <p>Agrega un nuevo cliente y asigna su membresía inicial.</p>
                    </div>
                </div>
                <button type="button" class="add-person-modal-close" data-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createPersonForm">
                <div class="modal-body add-person-modal-body">
                    <div class="add-person-information">
                        <span>
                            <i class="fas fa-circle-info"></i>
                        </span>
                        <p>Al registrar a la persona se generará automáticamente un folio único.</p>
                    </div>

                    <div class="form-group add-person-form-group">
                        <label for="persona_nombre">Nombre completo</label>
                        <div class="add-person-input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="persona_nombre" class="form-control add-person-input" placeholder="Ingrese el nombre completo" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="form-group add-person-form-group">
                        <label for="persona_membresia">Membresía</label>
                        <div class="add-person-input-icon">
                            <i class="fas fa-id-card"></i>
                            <select id="persona_membresia" class="form-control add-person-input add-person-select" required>
                                <option value="">Seleccione una membresía</option>

                                <?php while (
                                    $membresia = $resultMembresias->fetch_assoc()
                                ): ?>
                                    <option value="<?php echo $membresia['id']; ?>" data-dias="<?php echo $membresia['dias']; ?>">
                                        <?php echo htmlspecialchars(
                                            $membresia['nombre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="add-person-dates-heading">
                        <div>
                            <h5>Periodo de membresía</h5>
                            <p>Las fechas se calculan automáticamente según el plan.</p>
                        </div>
                        <span>
                            <i class="fas fa-calendar-days"></i>
                        </span>
                    </div>

                    <div class="add-person-form-grid">
                        <div class="form-group add-person-form-group">
                            <label for="fecha_ini_persona">Fecha de inicio</label>
                            <input type="date" id="fecha_ini_persona" class="form-control add-person-input" required>
                        </div>

                        <div class="form-group add-person-form-group">
                            <label for="fecha_fin_persona">Fecha de vencimiento</label>
                            <input type="date" id="fecha_fin_persona" class="form-control add-person-input" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer add-person-modal-footer">
                    <button type="reset" class="add-person-button add-person-button-secondary">
                        <i class="fas fa-rotate-left"></i>
                        Limpiar
                    </button>

                    <button type="submit" class="add-person-button add-person-button-primary">
                        <i class="fas fa-user-check"></i>
                        Registrar persona
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>