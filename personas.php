<?php
session_start();

if (!isset($_SESSION['telefono'])) {
    header("Location: index.php");
    exit();
}

require_once 'php_action/conn_db.php';

// TOTAL DE PERSONAS
$sqlTotal = "
SELECT COUNT(*) AS personas
FROM personas
WHERE estatus = 1
";

$resultTotal = $connect->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();

$total_personas = $rowTotal['personas'];
?>

<!DOCTYPE html>
<html lang="es">
<head>

    <title>Personas | ProfitnessGym</title>

    <?php include 'includes/head.php' ?>
    <!-- CSS -->
    <link rel="stylesheet" href="css/personas.css">


</head>

<body>

<?php include 'includes/navbar.php' ?>

<!-- CONTENIDO -->

<div class="content-card">

    <!-- HEADER -->

    <div class="header-toolbar">

        <div>

            <h2>

                <i class="fas fa-users"></i>
                Personas Registradas

            </h2>

            <p>

                Total de personas activas:

                <strong>

                    <?php echo $total_personas; ?>

                </strong>

            </p>

        </div>

        <button
            class="btn-add-person"
            data-toggle="modal"
            data-target="#addPersonModal">

            <i class="fas fa-user-plus"></i>

            Agregar Persona

        </button>

    </div>

    <!-- BUSCADOR -->

    <div class="search-box">

        <i class="fas fa-search"></i>

        <input
            type="text"
            id="searchPerson"
            placeholder="Buscar persona por nombre o folio">

    </div>

    <!-- TABLA -->

    <div class="table-responsive">

        <table
            id="personsTable"
            class="table modern-table">

            <thead>

            <tr>

                <th>Nombre</th>
                <th>Folio</th>
                <th>Inicio</th>
                <th>Vencimiento</th>
                <th>Estatus</th>
                <th>Acciones</th>

            </tr>

            </thead>

            <tbody>

            <?php

            $fechaActual = new DateTime();

            $sql = "
            SELECT *
            FROM personas
            WHERE estatus = 1
            ORDER BY nombre ASC
            ";

            $result = $connect->query($sql);

            if($result->num_rows > 0){

                while($row = $result->fetch_assoc()){

                    $fechaInicio =
                    date(
                        "d/m/Y",
                        strtotime($row['fecha_ini'])
                    );

                    $fechaFin =
                    date(
                        "d/m/Y",
                        strtotime($row['fecha_fin'])
                    );

                    $fechaFinObj =
                    new DateTime(
                        $row['fecha_fin']
                    );

                    if($fechaActual <= $fechaFinObj){

                        $badge =
                        "<span class='status-active'>
                        Activo
                        </span>";

                    }else{

                        $badge =
                        "<span class='status-expired'>
                        Vencido
                        </span>";

                    }

                    echo "

                    <tr>

                        <td>{$row['nombre']}</td>

                        <td>

                            <span
                                id='folio-{$row['id']}'
                                class='folio-hidden'>

                                **********

                            </span>

                            <button
                                type='button'
                                class='btn btn-sm btn-link toggleFolioBtn'
                                data-id='{$row['id']}'
                                data-folio='{$row['folio']}'>

                                <i class='fas fa-eye'></i>

                            </button>

                        </td>

                        <td>{$fechaInicio}</td>

                        <td>{$fechaFin}</td>

                        <td>{$badge}</td>

                        <td>

                            <button
                                type='button'
                                class='btn-edit editBtn'
                                
                                data-id='{$row['id']}'
                                data-nombre='{$row['nombre']}'
                                data-folio='{$row['folio']}'
                                data-fecha_ini='{$row['fecha_ini']}'
                                data-fecha_fin='{$row['fecha_fin']}'>

                                <i class='fas fa-pen'></i>

                            </button>

                            <button
                                type='button'
                                class='btn-delete deleteBtn ml-2'
                                
                                data-id='{$row['id']}'
                                data-nombre='{$row['nombre']}'>

                                <i class='fas fa-trash'></i>

                            </button>

                        </td>

                    </tr>

                    ";

                }

            }else{

                echo "

                <tr>

                    <td
                        colspan='6'
                        class='text-center'>

                        No hay personas registradas

                    </td>

                </tr>

                ";

            }

            ?>

            </tbody>

        </table>

    </div>

</div>

<!-- MODAL EDITAR PERSONA -->

<div
    class="modal fade"
    id="editPersonModal"
    tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header">
                <h5 class="modal-title">

                    <i class="fas fa-user-edit"></i>
                    Editar Persona

                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>
            </div>

            <div class="modal-body">
                <form id="editPersonForm">
                    <input
                        type="hidden"
                        id="edit_id">

                    <div class="form-group">
                        <label for="edit_nombre">

                            Nombre

                        </label>

                        <input
                            type="text"
                            id="edit_nombre"
                            class="form-control modern-input"
                            required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fecha_ini">

                                    Fecha Inicio

                                </label>
                                <input
                                    type="date"
                                    id="edit_fecha_ini"
                                    class="form-control modern-input"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fecha_fin">

                                    Fecha Fin

                                </label>
                                <input
                                    type="date"
                                    id="edit_fecha_fin"
                                    class="form-control modern-input"
                                    required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn-cancel"
                    data-dismiss="modal">

                    <i class="fas fa-times"></i>
                    Cancelar

                </button>

                <button
                    type="submit"
                    form="editPersonForm"
                    class="btn-save">

                    <i class="fas fa-save"></i>
                    Guardar Cambios

                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/modals/add_person_modal.php' ?>

<?php include 'includes/footer.php' ?>

<!--Scripts funcionales-->
<script src="js/personas.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/alerts.js"></script>
<script src="js/personas/add_person.js"></script>

</body>
</html>

<?php
$connect->close();
?>