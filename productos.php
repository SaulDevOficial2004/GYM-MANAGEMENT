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
    FROM productos
";

$resultTotal = $connect->query($sqlTotal);

$rowTotal = $resultTotal->fetch_assoc();

$totalProductos = $rowTotal['total'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Productos | ProfitnessGym</title>

    <?php include 'includes/head.php'; ?>

    <link rel="stylesheet"
        href="css/productos.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container-fluid page-container">
    <div class="card-custom">
        <div class="header-toolbar">
            <div>
                <h2>
                    <i class="fas fa-box-open"></i>
                    Productos
                </h2>

                <p>
                    Total de productos:
                    <strong>
                        <?php echo $totalProductos; ?>
                    </strong>
                </p>

            </div>

            <button
                type="button"
                class="btn-add"
                data-toggle="modal"
                data-target="#addProductModal">

                <i class="fas fa-plus"></i>

                Nuevo Producto

            </button>

        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input
                type="text"
                id="searchProduct"
                placeholder="Buscar producto">
        </div>

        <div class="table-responsive">

            <table
                class="table custom-table"
                id="productTable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>

<?php

$sql = "
    SELECT *
    FROM productos
    ORDER BY nombre ASC
";

$result = $connect->query($sql);

while($row = $result->fetch_assoc()){

    //BOTON ELIMINAR
    $botonEliminar = "

        <button
            class='btn-remove deleteProductBtn'
            data-id='{$row['id']}'
            data-nombre=\"{$row['nombre']}\">

            <i class='fas fa-trash'></i>
        </button>

    ";

    $botonVenta = "

    <button
        class='btn-sale sellProductBtn'
        data-id='{$row['id']}'
        data-nombre=\"{$row['nombre']}\"
        data-precio='{$row['precio']}'
        data-stock='{$row['stock']}'>

        <i class='fas fa-cash-register'></i>

    </button>

    ";

    //ESTADO DEL BOTON
    $botonEstado = '';

    if($row['activo'] == 1){

        $botonEstado = "

        <button
            class='btn-delete toggleProductBtn'
            data-id='{$row['id']}'
            data-estado='1'
            data-nombre=\"{$row['nombre']}\">

            <i class='fas fa-ban'></i>
        </button>

        ";

    }else{

        $botonEstado = "

        <button
            class='btn-enable toggleProductBtn'
            data-id='{$row['id']}'
            data-estado='0'
            data-nombre=\"{$row['nombre']}\">

            <i class='fas fa-check'></i>
        </button>

        ";

    }
    //ESTADO BOTON
    $estado = $row['activo'] == 1

    ?

    "<span class='status-active'>
        Activo
    </span>"

    :

    "<span class='status-expired'>
        Inactivo
    </span>";

    echo "

        <tr>
            <td>
                {$row['nombre']}
            </td>
            <td>
                {$row['descripcion']}
            </td>
            <td>
                $" . number_format($row['precio'],2) . "
            </td>
            <td>
                {$row['stock']}
            </td>
            <td>
                {$estado}
            </td>
            <td>

                <button
                    class='btn-edit editProductBtn'
                    data-id='{$row['id']}'
                    data-nombre=\"{$row['nombre']}\"
                    data-descripcion=\"{$row['descripcion']}\"
                    data-precio='{$row['precio']}'
                    data-stock='{$row['stock']}'>

                    <i class='fas fa-pen'></i>

                </button>

                {$botonEstado}

                {$botonEliminar}

                {$botonVenta}

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

<!--MODAL VENDER PRODUCTO-->
<div class="modal fade" id="sellProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4 class="modal-title">

                    Vender Producto

                </h4>
            </div>

            <form id="sellProductForm">
                <input
                    type="hidden"
                    id="sell_id">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Producto</label>

                        <input
                            type="text"
                            id="sell_nombre"
                            class="form-control modern-input"
                            readonly>

                    </div>

                    <div class="form-group">
                        <label>Cantidad</label>

                        <input
                            type="number"
                            id="sell_cantidad"
                            min="1"
                            value="1"
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

                        Vender

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CREAR PRODUCTO -->

<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4 class="modal-title">

                    <i class="fas fa-plus-circle"></i>

                    Nuevo Producto

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="createProductForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>

                        <input
                            type="text"
                            id="nombre"
                            class="form-control modern-input"
                            placeholder="Nombre del producto"
                            required>

                    </div>

                    <div class="form-group">
                        <label>Descripción</label>

                        <textarea
                            id="descripcion"
                            rows="3"
                            class="form-control modern-input"
                            placeholder="Descripción"></textarea>

                    </div>

                    <div class="form-group">
                        <label>Precio</label>

                        <input
                            type="number"
                            step="0.01"
                            id="precio"
                            class="form-control modern-input"
                            placeholder="0.00"
                            required>

                    </div>

                    <div class="form-group">
                        <label>Stock</label>

                        <input
                            type="number"
                            id="stock"
                            class="form-control modern-input"
                            placeholder="0"
                            required>

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

<!-- MODAL EDITAR PRODUCTO -->

<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h4 class="modal-title">

                    <i class="fas fa-pen"></i>

                    Editar Producto

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="editProductForm">
                <input
                    type="hidden"
                    id="edit_id">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>

                        <input
                            type="text"
                            id="edit_nombre"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">
                        <label>Descripción</label>

                        <textarea
                            id="edit_descripcion"
                            rows="3"
                            class="form-control modern-input"></textarea>

                    </div>

                    <div class="form-group">
                        <label>Precio</label>

                        <input
                            type="number"
                            step="0.01"
                            id="edit_precio"
                            class="form-control modern-input"
                            required>

                    </div>

                    <div class="form-group">
                        <label>Stock</label>

                        <input
                            type="number"
                            id="edit_stock"
                            class="form-control modern-input"
                            required>

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

<?php include 'includes/footer.php'; ?>

<script src="js/productos.js"></script>

</body>
</html>

<?php

$connect->close();

?>