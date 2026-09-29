<?php

require_once __DIR__ . '/includes/auth.php';

requireRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

require_once __DIR__ . '/php_action/conn_db.php';

use GMS\Repository\ProductoRepository;

$telefono = $_SESSION['telefono'];
$nombre = $_SESSION['nombre'];

$productoRepo = new ProductoRepository($connect);

$paginaActual = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 25;

$statsProductos = $productoRepo->estadisticas();

$totalProductos = $statsProductos['total'];
$totalActivos = $statsProductos['activos'];
$totalInactivos = $statsProductos['inactivos'];
$totalStock = $statsProductos['stock'];
$totalStockBajo = $statsProductos['stock_bajo'];
$valorInventario = $statsProductos['valor'];

$totalPaginas = max(1, (int) ceil($totalProductos / $porPagina));

if ($paginaActual > $totalPaginas) {
    $paginaActual = $totalPaginas;
}

$productos = $productoRepo->listar($porPagina, ($paginaActual - 1) * $porPagina);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Productos | GMS</title>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="css/productos.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="products-container">
    <header class="products-header">
        <div>
            <p class="products-eyebrow">Inventario y ventas</p>
            <h1>Productos</h1>
            <p>Administra el inventario, precios, disponibilidad y ventas del gimnasio.</p>
        </div>

        <button
            type="button"
            class="product-add-button"
            data-toggle="modal"
            data-target="#addProductModal">

            <i class="fas fa-plus"></i>
            <span>Nuevo producto</span>
        </button>
    </header>

    <section class="products-stats-grid">
        <article class="product-stat-card stat-total">
            <span class="product-stat-icon">
                <i class="fas fa-box-open"></i>
            </span>

            <div>
                <small>Total de productos</small>
                <strong><?php echo $totalProductos; ?></strong>
                <p>Productos registrados</p>
            </div>
        </article>

        <article class="product-stat-card stat-stock">
            <span class="product-stat-icon">
                <i class="fas fa-boxes"></i>
            </span>

            <div>
                <small>Unidades disponibles</small>
                <strong><?php echo $totalStock; ?></strong>
                <p>Stock total del inventario</p>
            </div>
        </article>

        <article class="product-stat-card stat-low">
            <span class="product-stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </span>

            <div>
                <small>Stock bajo</small>
                <strong><?php echo $totalStockBajo; ?></strong>
                <p>Productos con 5 unidades o menos</p>
            </div>
        </article>

        <article class="product-stat-card stat-value">
            <span class="product-stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </span>

            <div>
                <small>Valor del inventario</small>
                <strong>
                    $<?php echo number_format($valorInventario, 2); ?>
                </strong>
                <p>Valor estimado del stock actual</p>
            </div>
        </article>
    </section>

    <section class="products-panel">
        <div class="products-toolbar">
            <div class="products-toolbar-heading">
                <h2>Catálogo de productos</h2>
                <p>Busca, filtra y administra el inventario disponible.</p>
            </div>

            <div class="products-search">
                <i class="fas fa-search"></i>

                <input
                    type="text"
                    id="searchProduct"
                    placeholder="Buscar por nombre o descripción"
                    autocomplete="off">

                <button
                    type="button"
                    id="clearProductSearch"
                    title="Limpiar búsqueda">

                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="products-filters">
            <div class="product-filter-buttons">
                <button
                    type="button"
                    class="product-filter-button active"
                    data-filter="all">

                    Todos
                </button>

                <button
                    type="button"
                    class="product-filter-button"
                    data-filter="active">

                    Activos
                </button>

                <button
                    type="button"
                    class="product-filter-button"
                    data-filter="low">

                    Stock bajo
                </button>

                <button
                    type="button"
                    class="product-filter-button"
                    data-filter="out">

                    Agotados
                </button>

                <button
                    type="button"
                    class="product-filter-button"
                    data-filter="inactive">

                    Inactivos
                </button>
            </div>

            <div class="products-results-count">
                <span id="visibleProductsCount">
                    <?php echo $totalProductos; ?>
                </span>

                <small>productos mostrados</small>
            </div>
        </div>

        <div class="products-grid" id="productsList">
            <?php if ($totalProductos > 0): ?>
                <?php foreach ($productos as $producto): ?>
                    <?php
                    $productoId = (int) $producto['id'];
                    $activo = (int) $producto['activo'] === 1;
                    $stock = (int) $producto['stock'];
                    $precio = (float) $producto['precio'];

                    $nombreSeguro = htmlspecialchars(
                        $producto['nombre'],
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $descripcionSegura = htmlspecialchars(
                        $producto['descripcion'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    if ($stock <= 0) {
                        $stockEstado = 'out';
                        $stockTexto = 'Agotado';
                        $stockClase = 'product-stock-out';
                    } elseif ($stock <= 5) {
                        $stockEstado = 'low';
                        $stockTexto = 'Stock bajo';
                        $stockClase = 'product-stock-low';
                    } else {
                        $stockEstado = 'normal';
                        $stockTexto = 'Disponible';
                        $stockClase = 'product-stock-normal';
                    }

                    $estadoFiltro = $activo
                        ? 'active'
                        : 'inactive';
                    ?>

                    <article
                        class="product-card <?php echo !$activo ? 'product-disabled' : ''; ?>"
                        data-status="<?php echo $estadoFiltro; ?>"
                        data-stock-status="<?php echo $stockEstado; ?>"
                        data-search="<?php echo strtolower(
                            $nombreSeguro . ' ' . $descripcionSegura
                        ); ?>">

                        <div class="product-card-header">
                            <div class="product-title-area">
                                <span class="product-card-icon">
                                    <i class="fas fa-box"></i>
                                </span>

                                <div>
                                    <h3><?php echo $nombreSeguro; ?></h3>

                                    <span class="product-code">
                                        <i class="fas fa-barcode"></i>
                                        Producto #<?php echo $productoId; ?>
                                    </span>
                                </div>
                            </div>

                            <?php if ($activo): ?>
                                <span class="product-status-active">
                                    <i class="fas fa-check-circle"></i>
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="product-status-inactive">
                                    <i class="fas fa-times-circle"></i>
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="product-description">
                            <?php if ($descripcionSegura !== ''): ?>
                                <p><?php echo $descripcionSegura; ?></p>
                            <?php else: ?>
                                <p class="product-description-empty">
                                    Sin descripción registrada.
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="product-information-grid">
                            <div class="product-price-box">
                                <small>Precio de venta</small>

                                <strong>
                                    $<?php echo number_format($precio, 2); ?>
                                </strong>
                            </div>

                            <div class="product-stock-box">
                                <div>
                                    <small>Unidades disponibles</small>
                                    <strong><?php echo $stock; ?></strong>
                                </div>

                                <span class="<?php echo $stockClase; ?>">
                                    <?php echo $stockTexto; ?>
                                </span>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="product-sale-button sellProductBtn"
                            data-id="<?php echo $productoId; ?>"
                            data-nombre="<?php echo $nombreSeguro; ?>"
                            data-precio="<?php echo $precio; ?>"
                            data-stock="<?php echo $stock; ?>"
                            <?php echo (!$activo || $stock <= 0) ? 'disabled' : ''; ?>>

                            <i class="fas fa-cash-register"></i>

                            <span>
                                <?php echo $stock <= 0 ? 'Producto agotado' : 'Vender producto'; ?>
                            </span>
                        </button>

                        <div class="product-card-footer">
                            <button
                                type="button"
                                class="product-action-button action-edit editProductBtn"
                                data-id="<?php echo $productoId; ?>"
                                data-nombre="<?php echo $nombreSeguro; ?>"
                                data-descripcion="<?php echo $descripcionSegura; ?>"
                                data-precio="<?php echo $precio; ?>"
                                data-stock="<?php echo $stock; ?>">

                                <i class="fas fa-pen"></i>
                                <span>Editar</span>
                            </button>

                            <?php if ($activo): ?>
                                <button
                                    type="button"
                                    class="product-action-button action-disable toggleProductBtn"
                                    data-id="<?php echo $productoId; ?>"
                                    data-estado="1"
                                    data-nombre="<?php echo $nombreSeguro; ?>">

                                    <i class="fas fa-ban"></i>
                                    <span>Desactivar</span>
                                </button>
                            <?php else: ?>
                                <button
                                    type="button"
                                    class="product-action-button action-enable toggleProductBtn"
                                    data-id="<?php echo $productoId; ?>"
                                    data-estado="0"
                                    data-nombre="<?php echo $nombreSeguro; ?>">

                                    <i class="fas fa-check"></i>
                                    <span>Activar</span>
                                </button>
                            <?php endif; ?>

                            <button
                                type="button"
                                class="product-action-button action-delete deleteProductBtn"
                                data-id="<?php echo $productoId; ?>"
                                data-nombre="<?php echo $nombreSeguro; ?>">

                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <div
                class="products-empty-state <?php echo $totalProductos > 0 ? 'd-none' : ''; ?>"
                id="productsEmptyState">

                <span>
                    <i class="fas fa-box-open"></i>
                </span>

                <h3>No se encontraron productos</h3>
                <p>Prueba con otro término o cambia el filtro seleccionado.</p>
            </div>
        </div>
        <?php include __DIR__ . '/includes/partials/paginacion.php'; ?>
    </section>
</main>

<!-- MODAL VENDER PRODUCTO -->

<div
    class="modal fade"
    id="sellProductModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="sellProductTitle"
    aria-hidden="true">

    <div
        class="modal-dialog product-modal-dialog modal-dialog-centered"
        role="document">

        <div class="modal-content product-modal">
            <div class="modal-header product-modal-header">
                <div class="product-modal-heading">
                    <span class="product-modal-icon modal-icon-success">
                        <i class="fas fa-cash-register"></i>
                    </span>

                    <div>
                        <h4 id="sellProductTitle">
                            Vender producto
                        </h4>

                        <p>
                            Registra una venta y descuenta unidades del inventario.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="product-modal-close"
                    data-dismiss="modal"
                    aria-label="Cerrar">

                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="sellProductForm">
                <input
                    type="hidden"
                    id="sell_id">

                <div class="modal-body product-modal-body">
                    <div class="sell-product-summary">
                        <span class="sell-product-icon">
                            <i class="fas fa-box"></i>
                        </span>

                        <div>
                            <small>Producto seleccionado</small>
                            <strong id="sellProductNameDisplay">
                                Producto
                            </strong>
                        </div>
                    </div>

                    <div class="product-form-grid">
                        <div class="form-group product-form-group">
                            <label for="sell_nombre">
                                Producto
                            </label>

                            <input
                                type="text"
                                id="sell_nombre"
                                class="form-control product-input product-input-readonly"
                                readonly>
                        </div>

                        <div class="form-group product-form-group">
                            <label for="sell_precio">
                                Precio unitario
                            </label>

                            <input
                                type="text"
                                id="sell_precio"
                                class="form-control product-input product-input-readonly"
                                readonly>
                        </div>
                    </div>

                    <div class="product-form-grid">
                        <div class="form-group product-form-group">
                            <label for="sell_stock">
                                Stock disponible
                            </label>

                            <input
                                type="number"
                                id="sell_stock"
                                class="form-control product-input product-input-readonly"
                                readonly>
                        </div>

                        <div class="form-group product-form-group">
                            <label for="sell_cantidad">
                                Cantidad
                            </label>

                            <input
                                type="number"
                                id="sell_cantidad"
                                min="1"
                                value="1"
                                class="form-control product-input"
                                required>
                        </div>
                    </div>

                    <div class="sell-total-card">
                        <div>
                            <small>Total estimado</small>
                            <strong id="sellProductTotal">
                                $0.00
                            </strong>
                        </div>

                        <span>
                            <i class="fas fa-receipt"></i>
                        </span>
                    </div>
                </div>

                <div class="modal-footer product-modal-footer">
                    <button
                        type="button"
                        class="product-modal-button modal-button-secondary"
                        data-dismiss="modal">

                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="product-modal-button modal-button-success"
                        id="sellProductSubmitBtn">

                        <i class="fas fa-check"></i>
                        Confirmar venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CREAR PRODUCTO -->

<div
    class="modal fade"
    id="addProductModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="addProductTitle"
    aria-hidden="true">

    <div
        class="modal-dialog product-modal-dialog modal-dialog-centered"
        role="document">

        <div class="modal-content product-modal">
            <div class="modal-header product-modal-header">
                <div class="product-modal-heading">
                    <span class="product-modal-icon modal-icon-success">
                        <i class="fas fa-plus"></i>
                    </span>

                    <div>
                        <h4 id="addProductTitle">
                            Nuevo producto
                        </h4>

                        <p>
                            Agrega un nuevo artículo al inventario.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="product-modal-close"
                    data-dismiss="modal"
                    aria-label="Cerrar">

                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createProductForm">
                <div class="modal-body product-modal-body">
                    <div class="form-group product-form-group">
                        <label for="nombre">
                            Nombre
                        </label>

                        <div class="product-input-icon">
                            <i class="fas fa-box"></i>

                            <input
                                type="text"
                                id="nombre"
                                class="form-control product-input"
                                placeholder="Nombre del producto"
                                required>
                        </div>
                    </div>

                    <div class="form-group product-form-group">
                        <label for="descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            rows="3"
                            class="form-control product-input product-textarea"
                            placeholder="Descripción del producto"></textarea>
                    </div>

                    <div class="product-inventory-heading">
                        <div>
                            <h5>Precio e inventario</h5>
                            <p>Define el precio de venta y las unidades iniciales.</p>
                        </div>

                        <span>
                            <i class="fas fa-boxes"></i>
                        </span>
                    </div>

                    <div class="product-form-grid">
                        <div class="form-group product-form-group">
                            <label for="precio">
                                Precio
                            </label>

                            <div class="product-currency-input">
                                <span>$</span>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="precio"
                                    class="form-control product-input"
                                    placeholder="0.00"
                                    required>
                            </div>
                        </div>

                        <div class="form-group product-form-group">
                            <label for="stock">
                                Stock inicial
                            </label>

                            <div class="product-input-icon">
                                <i class="fas fa-cubes"></i>

                                <input
                                    type="number"
                                    min="0"
                                    id="stock"
                                    class="form-control product-input"
                                    placeholder="0"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer product-modal-footer">
                    <button
                        type="reset"
                        class="product-modal-button modal-button-secondary">

                        <i class="fas fa-undo"></i>
                        Limpiar
                    </button>

                    <button
                        type="submit"
                        class="product-modal-button modal-button-primary"
                        id="createProductBtn">

                        <i class="fas fa-check"></i>
                        Guardar producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR PRODUCTO -->

<div
    class="modal fade"
    id="editProductModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="editProductTitle"
    aria-hidden="true">

    <div
        class="modal-dialog product-modal-dialog modal-dialog-centered"
        role="document">

        <div class="modal-content product-modal">
            <div class="modal-header product-modal-header">
                <div class="product-modal-heading">
                    <span class="product-modal-icon modal-icon-primary">
                        <i class="fas fa-pen"></i>
                    </span>

                    <div>
                        <h4 id="editProductTitle">
                            Editar producto
                        </h4>

                        <p>
                            Actualiza la información, precio y existencias.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    class="product-modal-close"
                    data-dismiss="modal"
                    aria-label="Cerrar">

                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editProductForm">
                <input
                    type="hidden"
                    id="edit_id">

                <div class="modal-body product-modal-body">
                    <div class="form-group product-form-group">
                        <label for="edit_nombre">
                            Nombre
                        </label>

                        <div class="product-input-icon">
                            <i class="fas fa-box"></i>

                            <input
                                type="text"
                                id="edit_nombre"
                                class="form-control product-input"
                                required>
                        </div>
                    </div>

                    <div class="form-group product-form-group">
                        <label for="edit_descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="edit_descripcion"
                            rows="3"
                            class="form-control product-input product-textarea"></textarea>
                    </div>

                    <div class="product-inventory-heading">
                        <div>
                            <h5>Precio e inventario</h5>
                            <p>Modifica el precio y las unidades disponibles.</p>
                        </div>

                        <span>
                            <i class="fas fa-boxes"></i>
                        </span>
                    </div>

                    <div class="product-form-grid">
                        <div class="form-group product-form-group">
                            <label for="edit_precio">
                                Precio
                            </label>

                            <div class="product-currency-input">
                                <span>$</span>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    id="edit_precio"
                                    class="form-control product-input"
                                    required>
                            </div>
                        </div>

                        <div class="form-group product-form-group">
                            <label for="edit_stock">
                                Stock
                            </label>

                            <div class="product-input-icon">
                                <i class="fas fa-cubes"></i>

                                <input
                                    type="number"
                                    min="0"
                                    id="edit_stock"
                                    class="form-control product-input"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer product-modal-footer">
                    <button
                        type="button"
                        class="product-modal-button modal-button-secondary"
                        data-dismiss="modal">

                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="product-modal-button modal-button-primary"
                        id="updateProductBtn">

                        <i class="fas fa-check"></i>
                        Guardar cambios
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
