<?php

require_once 'php_action/conn_db.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Consulta de Membresía | ProfitnessGym</title>

    <link rel="shortcut icon"
          href="img/logo_pfg-removebg-preview.ico"
          type="image/x-icon">

    <!-- Bootstrap -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- FontAwesome -->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- SweetAlert2 -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- CSS -->

    <link rel="stylesheet"
          href="css/auto_busqueda.css">

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-custom">

    <a class="navbar-brand d-flex align-items-center"
       href="#">

        <img src="img/logo_pfg-removebg-preview.png"
             class="nav-logo">

        <span class="brand-text">

            ProfitnessGym

        </span>

    </a>

</nav>

<!-- CONTENIDO -->

<div class="search-card">

    <!-- HEADER -->

    <div class="search-header">

        <h1>

            <i class="fas fa-search"></i>

            Consulta tu Membresía

        </h1>

        <p>

            Busca tu nombre para verificar el estado actual de tu membresía

        </p>

    </div>

    <!-- BUSCADOR -->

    <div class="search-box">

        <i class="fas fa-user"></i>

        <input
            type="text"
            id="clientSearch"
            placeholder="Escribe tu nombre, apellido o folio">

    </div>

    <!-- ESTADO VACÍO -->

    <div
        id="emptyState"
        class="empty-state">

        <i class="fas fa-users"></i>

        <h4>

            Busca tu registro

        </h4>

        <p>

            Empieza escribiendo tu nombre en el buscador

        </p>

    </div>

    <!-- TABLA -->

    <div
        id="resultsContainer"
        class="table-responsive d-none">

        <table
            class="table modern-table">

            <thead>

                <tr>

                    <th>Nombre</th>

                    <th>Vencimiento</th>

                    <th>Estatus</th>

                </tr>

            </thead>

            <tbody id="resultTable">

            </tbody>

        </table>

    </div>

    <!-- CARDS MOVIL -->

    <div
        id="mobileResults"
        class="d-none">

    </div>

    <!-- BOTÓN SALIR -->

    <div class="text-center mt-4">

        <a href="inicio.php"
           class="btn-exit">

            <i class="fas fa-sign-out-alt"></i>

            Salir

        </a>

    </div>

</div>

<!-- FOOTER -->

<footer class="footer-custom">

    <img src="img/logo_pfg-removebg-preview.png"
         class="footer-logo">

    <h5>

        ProfitnessGym

    </h5>

    <p>

        Consulta rápida de membresías

    </p>

    <div class="social-links">

        <a href="https://www.facebook.com/p/Pro-Fitness-Gym-100042239455651/"
           target="_blank">

            <i class="fab fa-facebook"></i>

        </a>

        <a href="https://www.instagram.com/gym.pro_fitness/"
           target="_blank">

            <i class="fab fa-instagram"></i>

        </a>

    </div>

</footer>

<!-- JQuery -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JS -->

<script src="js/auto_busqueda.js"></script>

</body>

</html>