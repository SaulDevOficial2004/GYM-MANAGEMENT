<?php

if(!isset($connect)){
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
<div class="modal fade"
     id="addPersonModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4 class="modal-title">

                    <i class="fas fa-user-plus"></i>
                    Registrar Persona

                </h4>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="createPersonForm">
                <div class="modal-body">
                    <div class="form-group">

                        <label>Nombre completo</label>

                        <input type="text"
                               id="persona_nombre"
                               class="form-control modern-input"
                               placeholder="Ingrese nombre completo"
                               required>

                    </div>

                    <div class="form-group">

                        <label>Duración</label>

                        <select
                            id="persona_membresia"
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

                        <label>Fecha inicio</label>

                        <input type="date"
                               id="fecha_ini_persona"
                               class="form-control modern-input"
                               required>

                    </div>

                    <div class="form-group">

                        <label>Fecha vencimiento</label>

                        <input type="date"
                               id="fecha_fin_persona"
                               class="form-control modern-input"
                               required>

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="reset"
                            class="btn btn-cancel">

                        Limpiar

                    </button>

                    <button type="submit"
                            class="btn btn-save">

                        Registrar

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>