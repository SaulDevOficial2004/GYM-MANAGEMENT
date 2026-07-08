<?php

session_start();

if(!isset($_SESSION['telefono'])){

    header("Location: index.php");
    exit();

}

require_once 'php_action/conn_db.php';

/* TOTAL COACHES */

$sqlTotal = "
SELECT COUNT(*) AS total
FROM coaches
WHERE activo = 1
";

$resultTotal = $connect->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();

$total_coaches = $rowTotal['total'];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>Coaches | ProfitnessGym</title>

    <?php include 'includes/head.php' ?>
    <!-- CSS -->
    <link rel="stylesheet" href="css/coaches.css">

</head>

<body>

<?php include 'includes/navbar.php' ?>

<!-- CONTENIDO -->

<div class="content-card">

    <!-- HEADER -->

    <div class="header-toolbar">

        <div>

            <h2>

                <i class="fas fa-user-tie"></i>

                Coaches

            </h2>

            <p>

                Total de coaches:

                <strong>

                    <?php echo $total_coaches; ?>

                </strong>

            </p>

        </div>

        <button
            class="btn-add-coach"
            data-toggle="modal"
            data-target="#addCoachModal">

            <i class="fas fa-plus"></i>

            Agregar Coach

        </button>

    </div>

    <!-- BUSCADOR -->

    <div class="search-box">

        <i class="fas fa-search"></i>

        <input
            type="text"
            id="searchCoach"
            placeholder="Buscar coach por nombre o especialidad">

    </div>

    <!-- TABLA -->

    <div class="table-responsive">

        <table
            id="coachTable"
            class="table modern-table">

            <thead>

                <tr>

                    <th>Foto</th>

                    <th>Nombre</th>

                    <th>Edad</th>

                    <th>Especialidad</th>

                    <th>Estado</th>

                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $sql = "
            SELECT *
            FROM coaches
            ORDER BY nombre ASC
            ";

            $result = $connect->query($sql);

            if($result->num_rows > 0){

                while($row = $result->fetch_assoc()){

                $estado = $row['activo'] == 1

                    ? "<span class='status-active'>
                    Activo
                    </span>"

                    : "<span class='status-expired'>
                    Inactivo
                    </span>";
                    //ESTADO BOTON
                    $botonEstado = '';

                    if($row['activo'] == 1){

                        $botonEstado = "

                        <button
                            class='btn-delete toggleCoachBtn'

                            data-id='{$row['id']}'

                            data-estado='0'

                            data-nombre=\"{$row['nombre']}\">

                            <i class='fas fa-ban'></i>

                        </button>

                        ";

                    }else{

                        $botonEstado = "

                        <button
                            class='btn-enable toggleCoachBtn'

                            data-id='{$row['id']}'

                            data-estado='1'

                            data-nombre=\"{$row['nombre']}\">

                            <i class='fas fa-check'></i>

                        </button>

                        ";

                    }

                    echo "

                    <tr>

                        <td>

                            <img
                                src='{$row['foto']}'
                                class='coach-avatar'>

                        </td>

                        <td>

                            {$row['nombre']}

                        </td>

                        <td>

                            {$row['edad']}

                        </td>

                        <td>

                            {$row['especialidad']}

                        </td>

                        <td>

                            {$estado}

                        </td>

                        <td>

                            <button
                                class='btn-edit editCoachBtn'

                                data-id='{$row['id']}'

                                data-nombre='{$row['nombre']}'

                                data-edad='{$row['edad']}'

                                data-especialidad='{$row['especialidad']}'

                                data-descripcion='{$row['descripcion']}'

                                data-foto='{$row['foto']}'>

                                <i class='fas fa-pen'></i>

                            </button>

                            {$botonEstado}

                        </td>

                    </tr>

                    ";

                }

            }else{

                echo "

                <tr>

                    <td colspan='5'
                        class='text-center empty-state'>

                        <i class='fas fa-user-slash fa-2x'></i>

                        <br><br>

                        No hay coaches registrados

                    </td>

                </tr>

                ";

            }

            ?>

            </tbody>

        </table>

    </div>

</div>

<!-- MODAL AGREGAR COACH -->

<div class="modal fade"
     id="addCoachModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    <i class="fas fa-user-tie"></i>

                    Registrar Coach

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form
                id="createCoachForm"
                enctype="multipart/form-data">

                <div class="modal-body">

                    <div class="form-group">

                        <label>

                            Nombre completo

                        </label>

                        <input
                            type="text"
                            id="coach_nombre"
                            class="form-control modern-input"
                            placeholder="Ingrese nombre completo"
                            required>

                    </div>

                    <div class="form-group">

                        <label>

                            Edad

                        </label>

                        <input
                            type="number"
                            id="coach_edad"
                            class="form-control modern-input"
                            placeholder="Ingrese edad"
                            required>

                    </div>

                    <div class="form-group">

                        <label>

                            Especialidad

                        </label>

                        <input
                            type="text"
                            id="coach_especialidad"
                            class="form-control modern-input"
                            placeholder="Ej. Hipertrofia"
                            required>

                    </div>

                    <div class="form-group">

                        <label>

                            Descripción

                        </label>

                        <textarea
                            id="coach_descripcion"
                            rows="4"
                            class="form-control modern-input"
                            placeholder="Descripción del coach"
                            required></textarea>

                    </div>

                    <div class="form-group">

                        <label>

                            Fotografía

                        </label>

                        <input
                            type="file"
                            id="coach_foto"
                            class="form-control-file"
                            accept="image/*"
                            required>

                    </div>

                    <div class="text-center">

                        <img
                            id="previewCoach"
                            src=""
                            style="display:none;
                                   width:120px;
                                   height:120px;
                                   object-fit:cover;
                                   border-radius:50%;
                                   border:4px solid #16BFFD;">

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button
                        type="reset"
                        class="btn btn-cancel">

                        Limpiar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-save">

                        Guardar Coach

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- MODAL EDITAR COACH -->

<div class="modal fade"
     id="editCoachModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    <i class="fas fa-user-edit"></i>

                    Editar Coach

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form
                id="editCoachForm"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    id="edit_id">

                <div class="modal-body">

                    <div class="text-center mb-3">

                        <img
                            id="editPreviewCoach"
                            src=""
                            style="
                                width:120px;
                                height:120px;
                                object-fit:cover;
                                border-radius:50%;
                                border:4px solid #16BFFD;
                            ">

                    </div>

                    <div class="form-group">

                        <label>Nombre</label>

                        <input
                            type="text"
                            id="edit_nombre"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Edad</label>

                        <input
                            type="number"
                            id="edit_edad"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Especialidad</label>

                        <input
                            type="text"
                            id="edit_especialidad"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Descripción</label>

                        <textarea
                            id="edit_descripcion"
                            rows="4"
                            class="form-control modern-input"
                            required></textarea>

                    </div>

                    <div class="form-group">

                        <label>

                            Cambiar fotografía

                        </label>

                        <input
                            type="file"
                            id="edit_foto"
                            class="form-control-file"
                            accept="image/*">

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

                        Guardar Cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include 'includes/modals/add_person_modal.php' ?>

<?php include 'includes/footer.php' ?>

<script src="js/coaches.js"></script>
<script src="js/personas/add_person.js"></script>

</body>
</html>

<?php

$connect->close();

?>