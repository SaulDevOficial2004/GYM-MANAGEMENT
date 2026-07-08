<?php

session_start();

if(!isset($_SESSION['telefono'])){

    header("Location:index.php");
    exit();

}

require_once 'php_action/conn_db.php';


//TOTAL MEMBRESIAS


$sqlTotal = "
    SELECT COUNT(*) AS total
    FROM membresias
";

$resultTotal = $connect->query($sqlTotal);

$rowTotal = $resultTotal->fetch_assoc();

$totalMembresias = $rowTotal['total'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Membresías | ProfitnessGym</title>

    <?php include 'includes/head.php'; ?>

    <link rel="stylesheet"
          href="css/membresias.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container-fluid page-container">
    <div class="card-custom">
        <div class="header-toolbar">
            <div>
                <h2>
                    <i class="fas fa-credit-card"></i>
                    Membresías
                </h2>

                <p>
                    Total de membresías:
                    <strong>
                        <?php echo $totalMembresias; ?>
                    </strong>
                </p>

            </div>

            <button
                type="button"
                class="btn-add"
                data-toggle="modal"
                data-target="#addMembershipModal">
                <i class="fas fa-plus"></i>

                Nueva Membresía

            </button>

        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input
                type="text"
                id="searchMembership"
                placeholder="Buscar membresía">
        </div>

        <div class="table-responsive">

            <table
                class="table custom-table"
                id="membershipTable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Promoción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

<?php

$sql = "
    SELECT *
    FROM membresias
    ORDER BY nombre ASC
";

$result = $connect->query($sql);

while($row = $result->fetch_assoc()){

    //BOTON ELIMINAR
    $botonEliminar = "

        <button
            class='btn-remove deleteMembershipBtn'
            data-id='{$row['id']}'
            data-nombre=\"{$row['nombre']}\">

            <i class='fas fa-trash'></i>
        </button>

    ";

    //ESTADO DEL BOTON
    $botonEstado = '';

    if($row['activo'] == 1){

        $botonEstado = "

        <button
            class='btn-delete toggleMembershipBtn'
            data-id='{$row['id']}'
            data-estado='1'
            data-nombre=\"{$row['nombre']}\">

            <i class='fas fa-ban'></i>
        </button>

        ";

    }else{

        $botonEstado = "

        <button
            class='btn-enable toggleMembershipBtn'
            data-id='{$row['id']}'
            data-estado='0'
            data-nombre=\"{$row['nombre']}\">

            <i class='fas fa-check'></i>
        </button>

        ";

    }
    //ESTADO BOTON

    $promocion = $row['promocion'] == 1

    ?

    "<span class='status-active'>
        Sí
    </span>"

    :

    "<span class='status-expired'>
        No
    </span>";

    $estado = $row['activo'] == 1

    ?

    "<span class='status-active'>
        Activa
    </span>"

    :

    "<span class='status-expired'>
        Inactiva
    </span>";

    echo "

        <tr>
            <td>
                {$row['nombre']}
            </td>
            <td>
                $" . number_format($row['precio'],2) . "
            </td>
            <td>
                {$promocion}
            </td>
            <td>
                {$estado}
            </td>
            <td>
                <button
                    class='btn-edit editMembershipBtn'
                    data-id='{$row['id']}'
                    data-nombre=\"{$row['nombre']}\"
                    data-descripcion=\"{$row['descripcion']}\"
                    data-precio='{$row['precio']}'
                    data-promocion='{$row['promocion']}'
                    data-precio_promocion='{$row['precio_promocion']}'
                    data-dias='{$row['dias']}'>
                    <i class='fas fa-pen'></i>
                </button>

                {$botonEstado}

                {$botonEliminar}
                

            </td>


        </tr>

        ";

}
?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- MODAL CREAR MEMBRESIA -->
<div class="modal fade"
     id="addMembershipModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    <i class="fas fa-plus-circle"></i>

                    Nueva Membresía

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="createMembershipForm">

                <div class="modal-body">

                    <div class="form-group">

                        <label>

                            Nombre

                        </label>

                        <input
                            type="text"
                            id="nombre"
                            class="form-control modern-input"
                            placeholder="Ej. Mensualidad"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Días</label>

                        <input
                            type="number"
                            id="dias"
                            class="form-control modern-input"
                            placeholder="Días que es valida la membresia."
                            required>

                    </div>

                    <div class="form-group">

                        <label>

                            Descripción

                        </label>

                        <textarea
                            id="descripcion"
                            rows="3"
                            class="form-control modern-input"
                            placeholder="Descripción de la membresía"></textarea>

                    </div>

                    <div class="form-group">

                        <label>

                            Precio Normal

                        </label>

                        <input
                            type="number"
                            step="0.01"
                            id="precio"
                            class="form-control modern-input"
                            placeholder="0.00"
                            required>

                    </div>

                    <div class="form-group">

                        <div class="custom-control custom-switch">

                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="promocion">

                            <label
                                class="custom-control-label"
                                for="promocion">

                                Activar Promoción

                            </label>

                        </div>

                    </div>

                    <div
                        id="promoContainer"
                        class="form-group d-none">

                        <label>

                            Precio Promoción

                        </label>

                        <input
                            type="number"
                            step="0.01"
                            id="precio_promocion"
                            class="form-control modern-input"
                            placeholder="0.00">

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

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- MODAL EDITAR MEMBRESIA -->

<div class="modal fade"
     id="editMembershipModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    <i class="fas fa-pen"></i>

                    Editar Membresía

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="editMembershipForm">

                <input
                    type="hidden"
                    id="edit_id">

                <div class="modal-body">

                    <div class="form-group">

                        <label>

                            Nombre

                        </label>

                        <input
                            type="text"
                            id="edit_nombre"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Días</label>

                        <input
                            type="number"
                            id="edit_dias"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">

                        <label>

                            Descripción

                        </label>

                        <textarea
                            id="edit_descripcion"
                            rows="3"
                            class="form-control modern-input"
                            required></textarea>

                    </div>

                    <div class="form-group">

                        <label>

                            Precio Normal

                        </label>

                        <input
                            type="number"
                            step="0.01"
                            id="edit_precio"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">

                        <div class="custom-control custom-switch">

                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="edit_promocion">

                            <label
                                class="custom-control-label"
                                for="edit_promocion">

                                Activar Promoción

                            </label>

                        </div>

                    </div>

                    <div
                        id="editPromoContainer"
                        class="form-group d-none">

                        <label>

                            Precio Promoción

                        </label>

                        <input
                            type="number"
                            step="0.01"
                            id="edit_precio_promocion"
                            class="form-control modern-input">

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

<?php include 'includes/footer.php'; ?>

<script src="js/membresias.js"></script>
<script src="js/personas/add_person.js"></script>

</body>
</html>

<?php

$connect->close();

?>