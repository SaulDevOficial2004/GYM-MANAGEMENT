<?php
require_once 'php_action/conn_db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="description" content="Consulta el estado actual de tu membresía.">

    <title>Consulta de membresía | GYM MANAGEMENT</title>

    <?php include 'includes/head.php'; ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.min.css"
          integrity="sha384-dCW5imOdApH6OwpFau8cZNKjqVbJYnCA5q+8YsMYP3XwXKsV6Jfz1u6MZLnXaBsS"
          crossorigin="anonymous">

    <link rel="stylesheet" href="css/auto_busqueda.css">
</head>
<body>

<nav class="membership-navbar">
    <div class="container">
        <div class="membership-navbar-content">
            <a href="inicio.php" class="membership-brand">
                <span class="membership-brand-logo">
                    <img src="img/logo.png" alt="GYM MANAGEMENT">
                </span>

                <span class="membership-brand-text">
                    <strong>GYM MANAGEMENT</strong>
                    <small>Consulta de membresías</small>
                </span>
            </a>

            <a href="inicio.php" class="membership-back-button">
                <i class="fas fa-arrow-left"></i>
                Volver al inicio
            </a>
        </div>
    </div>
</nav>

<main class="membership-search-page">
    <div class="container">
        <section class="membership-search-card">
            <div class="membership-search-header">
                <span class="membership-search-icon">
                    <i class="fas fa-id-card"></i>
                </span>

                <span class="membership-search-label">
                    Consulta personal
                </span>

                <h1>Consulta tu membresía</h1>

                <p>
                    Escribe tu folio único para verificar el estado actual de tu membresía.
                </p>
            </div>

            <div class="membership-search-box">
                <span class="membership-search-box-icon">
                    <i class="fas fa-search"></i>
                </span>

                <input
                    type="search"
                    id="clientSearch"
                    placeholder="CLI-XXXXXX"
                    autocomplete="off"
                    spellcheck="false"
                    maxlength="10">

                <button
                    type="button"
                    id="clearSearchButton"
                    class="membership-search-clear d-none"
                    aria-label="Limpiar búsqueda">

                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="membership-search-help">
                <span>
                    <i class="fas fa-shield-alt"></i>
                    Consulta segura
                </span>

                <small>
                    Ingresa el folio completo para realizar la consulta.
                </small>
            </div>

            <div id="searchLoading" class="membership-search-loading d-none">
                <span>
                    <i class="fas fa-spinner fa-spin"></i>
                </span>

                <strong>Buscando membresías...</strong>

                <small>
                    Espera un momento.
                </small>
            </div>

            <div id="emptyState" class="empty-state">
                <span>
                    <i class="fas fa-user-check"></i>
                </span>

                <h4>Busca tu registro</h4>

                <p>
                    Ingresa el folio único que recibiste al registrarte.
                </p>
            </div>

            <div id="noResultsState" class="empty-state d-none">
                <span class="empty-state-warning">
                    <i class="fas fa-search"></i>
                </span>

                <h4>Sin coincidencias</h4>

                <p>
                    No encontramos registros relacionados con tu búsqueda.
                </p>
            </div>

            <div id="resultsSection" class="membership-results-section d-none">
                <div class="membership-results-header">
                    <div>
                        <span>Resultados</span>
                        <h2>Membresías encontradas</h2>
                    </div>

                    <strong id="resultsCount">
                        0 resultados
                    </strong>
                </div>

                <div id="resultsContainer" class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody id="resultTable"></tbody>
                    </table>
                </div>

                <div id="mobileResults" class="membership-mobile-results"></div>
            </div>
        </section>

        <section class="membership-information-card">
            <div class="membership-information-icon">
                <i class="fas fa-circle-info"></i>
            </div>

            <div>
                <h3>¿No encuentras tu membresía?</h3>

                <p>
                    Verifica que el nombre o folio esté escrito correctamente. También puedes solicitar apoyo directamente en recepción.
                </p>
            </div>
        </section>
    </div>
</main>

<footer class="membership-footer">
    <div class="container">
        <div class="membership-footer-content">
            <div class="membership-footer-brand">
                <span>
                    <img src="img/logo.png" alt="GYM MANAGEMENT">
                </span>

                <div>
                    <strong>GYM MANAGEMENT</strong>
                    <small>Consulta rápida de membresías</small>
                </div>
            </div>

            <div class="membership-footer-social">
                <a
                    href="https://www.facebook.com/p/Pro-Fitness-Gym-100042239455651/"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Facebook">

                    <i class="fab fa-facebook-f"></i>
                </a>

                <a
                    href="https://www.instagram.com/gym.pro_fitness/"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Instagram">

                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>

        <div class="membership-footer-bottom">
            <span>
                © <?php echo date('Y'); ?> GYM MANAGEMENT
            </span>

            <span>
                <i class="fas fa-shield-alt"></i>
                Consulta protegida
            </span>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>

<script src="js/auto_busqueda.js"></script>

</body>
</html>