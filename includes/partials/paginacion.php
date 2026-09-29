<?php if ($totalPaginas > 1): ?>
    <nav class="paginacion" aria-label="Paginación">
        <?php if ($paginaActual > 1): ?>
            <a class="paginacion-anterior" href="?pagina=<?php echo $paginaActual - 1; ?>">&laquo; Anterior</a>
        <?php endif; ?>
        <span class="paginacion-info">Página <?php echo $paginaActual; ?> de <?php echo $totalPaginas; ?></span>
        <?php if ($paginaActual < $totalPaginas): ?>
            <a class="paginacion-siguiente" href="?pagina=<?php echo $paginaActual + 1; ?>">Siguiente &raquo;</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>
