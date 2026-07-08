<?php

require_once 'php_action/conn_db.php';

$sqlCoaches = "

    SELECT *
    FROM coaches
    WHERE activo = 1
    ORDER BY nombre ASC
";

$resultCoaches = $connect->query($sqlCoaches);

//MEMBRESIAS ACTIVAS


$sqlMembresias = "

    SELECT *
    FROM membresias
    WHERE activo = 1
    ORDER BY precio ASC
";

$resultMembresias = $connect->query($sqlMembresias);
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        ProFitness Gym
    </title>

    <link rel="shortcut icon" href="img/logo_pfg-removebg-preview.ico" type="image/x-icon">

    <!-- Bootstrap -->

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- FontAwesome -->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- CSS -->

    <link rel="stylesheet"
          href="css/inicio.css">

    <link rel="stylesheet" href="css/components/modal.css">

</head>

<body>

<!-- ===================== -->
<!-- NAVBAR -->
<!-- ===================== -->

<nav class="navbar navbar-expand-lg navbar-custom">

    <div class="container">

        <a class="navbar-brand d-flex align-items-center"
           href="#">

            <img src="img/logo_pfg-removebg-preview.png"
                 class="nav-logo">

            <span class="brand-text">

                ProFitness Gym

            </span>

        </a>

        <button class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false">

            <i class="fas fa-bars"></i>

        </button>

        <div class="collapse navbar-collapse"
             id="navbarNav">

            <ul class="navbar-nav ml-auto">

                <li class="nav-item">

                    <a href="#inicio"
                    class="nav-link">

                        <i class="fas fa-home"></i>

                        Inicio

                    </a>

                </li>

                <li class="nav-item">

                    <a href="#planes"
                    class="nav-link">

                        <i class="fas fa-dumbbell"></i>

                        Planes

                    </a>

                </li>

                <li class="nav-item">

                    <a href="#coaches"
                    class="nav-link">

                        Coaches

                    </a>

                </li>

                <li class="nav-item">

                    <a href="#ubicacion"
                    class="nav-link">

                        <i class="fas fa-map-marker-alt"></i>

                        Ubicación

                    </a>

                </li>

                <li class="nav-item">

                    <a href="auto_busqueda.php"
                    class="nav-link membership-link">

                        <i class="fas fa-id-card"></i>

                        Ver Mi Membresía

                    </a>

                </li>

                <li class="nav-item">

                    <a href="#"
                    class="nav-link membership-link"
                    data-toggle="modal"
                    data-target="#transferenciaModal">

                        <i class="fas fa-money-check-alt"></i>

                        Transferencia

                    </a>

                </li>

                <li class="nav-item">

                    <a href="index.php"
                    class="btn-admin">

                        <i class="fas fa-lock"></i>

                        Panel Administrativo

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>

<!-- ===================== -->
<!-- HERO -->
<!-- ===================== -->

<section id="inicio"
         class="hero-section">

    <div class="hero-overlay">

        <div class="container text-center">

            <h1>

                TRANSFORMA TU CUERPO

            </h1>

            <p>

                El mejor lugar para alcanzar tus metas físicas
                y construir una versión más fuerte de ti mismo.

            </p>

            <div class="hero-features">

                <span>

                    <i class="fas fa-dumbbell"></i>

                    Área de Pesas

                </span>

                <span>

                    <i class="fas fa-heartbeat"></i>

                    Área de Cardio

                </span>

                <span>

                    <i class="fas fa-medal"></i>

                    Coaches con experiencia

                </span>

            </div>

            <div class="hero-buttons">

                <a href="auto_busqueda.php"
                   class="btn-membership">

                    <i class="fas fa-id-card"></i>

                    Ver Mi Membresía

                </a>

                <button
                    type="button"
                    class="btn-membership"
                    data-toggle="modal"
                    data-target="#transferenciaModal">

                    <i class="fas fa-money-check-alt"></i>

                    Pagar por Transferencia

                </button>

                <a href="#planes"
                   class="btn-plans">

                    <i class="fas fa-fire"></i>

                    Ver Planes

                </a>

            </div>

        </div>

    </div>

</section>

<!-- ===================== -->
<!-- HORARIOS -->
<!-- ===================== -->

<section class="schedule-section">

    <div class="container">

        <div class="section-title">

            <h2>

                <i class="fas fa-business-time"></i>

                Horarios de Atención

            </h2>

        </div>

        <div class="schedule-card">

            <div class="schedule-item">

                <h5>

                    Lunes - Viernes

                </h5>

                <p>

                    6:00 AM - 10:00 PM

                </p>

            </div>

            <div class="schedule-item">

                <h5>

                    Sábado

                </h5>

                <p>

                    7:00 AM - 3:00 PM

                </p>

            </div>

            <div class="schedule-item">

                <h5>

                    Domingo

                </h5>

                <p>

                    Cerrado

                </p>

            </div>

        </div>

    </div>

</section>

<!-- ===================== -->
<!-- ESTADISTICAS -->
<!-- ===================== -->

<section class="stats-section">

    <div class="container">

        <div class="row">

            <div class="col-md-3">

                <div class="stat-card">

                    <h2 class="counter"
                        data-target="250">

                        0

                    </h2>

                    <p>

                        Clientes Activos

                    </p>

                </div>

            </div>

            <div class="col-md-3">

                <div class="stat-card">

                    <h2 class="counter"
                        data-target="8">

                        0

                    </h2>

                    <p>

                        Años de Experiencia

                    </p>

                </div>

            </div>

            <div class="col-md-3">

                <div class="stat-card">

                    <h2 class="counter"
                        data-target="3">

                        0

                    </h2>

                    <p>

                        Entrenadores

                    </p>

                </div>

            </div>

            <div class="col-md-3">

                <div class="stat-card">

                    <h2 class="counter"
                        data-target="1200">

                        0

                    </h2>

                    <p>

                        Metas Cumplidas

                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ===================== -->
<!-- PLANES -->
<!-- ===================== -->

<section id="planes" class="plans-section">

    <div class="container">

        <div class="section-title">

            <h2>

                Planes Disponibles

            </h2>

        </div>

        <div class="row">
            <?php

                while(

                    $membresia =

                    $resultMembresias
                    ->fetch_assoc()

                ){

                ?>

                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="plan-card

                    <?php

                    echo

                    $membresia['promocion'] == 1

                    ?

                    'featured-plan'

                    :

                    '';

                    ?>">

                        <?php

                        if(

                            $membresia['promocion']

                            == 1

                        ){

                        ?>

                        <span class="plan-badge">

                            🔥 Promoción

                        </span>

                        <?php

                        }

                        ?>

                        <h3>

                            <?php

                            echo

                            $membresia['nombre'];

                            ?>

                        </h3>

                        <p>

                            <?php

                            echo

                            $membresia['descripcion'];

                            ?>

                        </p>

                        <?php

                        if(

                            $membresia['promocion']

                            == 1

                        ){

                        ?>

                        <div class="old-price">

                            $

                            <?php

                            echo number_format(

                                $membresia['precio'],

                                2

                            );

                            ?>

                        </div>

                        <h1 class="promo-price">

                            $

                            <?php

                            echo number_format(

                                $membresia['precio_promocion'],

                                2

                            );

                            ?>

                        </h1>

                        <?php

                        }else{

                        ?>

                        <h1>

                            $

                            <?php

                            echo number_format(

                                $membresia['precio'],

                                2

                            );

                            ?>

                        </h1>

                        <?php

                        }

                        ?>
                        </div>

                    </div>

                    <?php

                    }

                    ?>

            </div>

        </div>

</section>

<!-- COACHES -->

<!-- ===================== -->
<!-- COACHES -->
<!-- ===================== -->

<section
    id="coaches"
    class="coaches-section">

    <div class="container">

        <div class="section-title">

            <h2>

                <i class="fas fa-user-tie"></i>

                Nuestros Coaches

            </h2>

            <p>

                Entrenadores con experiencia listos para ayudarte a alcanzar tus objetivos.

            </p>

        </div>

        <div class="row">

            <?php

            if(
                $resultCoaches->num_rows > 0
            ){

                while(
                    $coach =
                    $resultCoaches->fetch_assoc()
                ){

            ?>

            <div class="col-md-4 mb-4">

                <div class="coach-card">

                    <img

                        src="<?php echo $coach['foto']; ?>"

                        class="coach-img">

                    <div class="coach-body">

                        <h4>

                            <?php
                            echo $coach['nombre'];
                            ?>

                        </h4>

                        <span
                            class="coach-speciality">

                            <?php
                            echo $coach['especialidad'];
                            ?>

                        </span>

                        <p>

                            <?php
                            echo $coach['descripcion'];
                            ?>

                        </p>

                    </div>

                </div>

            </div>

            <?php

                }

            }

            ?>

        </div>

    </div>

</section>

<!-- ===================== -->
<!-- UBICACION -->
<!-- ===================== -->

<section id="ubicacion"
         class="location-section">

    <div class="container">

        <div class="section-title">

            <h2>

                <i class="fas fa-map-marker-alt"></i>

                Encuéntranos

            </h2>

        </div>

        <div class="row align-items-center">

            <div class="col-lg-7">

                <div class="map-card">

                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3802.2324311551993!2d-101.48248002512588!3d17.63914719550424!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x84347924f969aec7%3A0x79bd078129b17493!2sPro%20Fitness%20Gym!5e0!3m2!1ses!2smx!4v1780528152659!5m2!1ses!2smx"
                        width="100%"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">

                    </iframe>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="location-info">

                    <h4>

                        <i class="fas fa-dumbbell"></i>

                        ProFitness Gym

                    </h4>

                    <p>

                        <i class="fas fa-location-dot"></i>

                        Zihuatanejo, Guerrero, Mexico

                    </p>

                    <p>

                        <i class="fas fa-clock"></i>

                        Lunes a Viernes
                        6:00 AM - 10:00 PM

                    </p>

                    <p>

                        <i class="fas fa-clock"></i>

                        Sabado
                        7:00 AM - 3:00 PM

                    </p>

                    <a href="#"
                       class="btn-membership">

                        <i class="fas fa-route"></i>

                        Cómo Llegar

                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

<footer class="footer">

    <div class="container text-center">

        <img src="img/logo_pfg-removebg-preview.png"
             class="footer-logo">

        <h5>

            ProFitness Gym

        </h5>

        <p>

            Transformando vidas todos los días.

        </p>

        <div class="social-links">

            <a href="https://www.facebook.com/profile.php?id=100042239455651">

                <i class="fab fa-facebook"></i>

            </a>

            <a href="https://www.instagram.com/gym.pro_fitness?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==">

                <i class="fab fa-instagram"></i>

            </a>

            <a href="#">

                <i class="fab fa-whatsapp"></i>

            </a>

        </div>

        <hr>

        <small>

            © 2026 ProFitness Gym.
            Todos los derechos reservados.

        </small>

    </div>

</footer>

<!-- MODAL TRANSFERENCIAS -->

<div class="modal fade"
     id="transferenciaModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    <i class="fas fa-university"></i>

                    Datos Bancarios

                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <?php

                $sqlTransferencia = "
                    SELECT *
                    FROM configuracion_transferencias
                    LIMIT 1
                ";

                $resultTransferencia =
                    $connect->query(
                        $sqlTransferencia
                    );

                $transferencia =
                    $resultTransferencia
                    ->fetch_assoc();

                ?>

                <div class="bank-card">

                    <h5>

                        Banco

                    </h5>

                    <p>

                        <?php
                        echo $transferencia['banco'];
                        ?>

                    </p>

                    <h5>

                        CLABE

                    </h5>

                    <p>

                        <?php
                        echo $transferencia['clabe'];
                        ?>

                    </p>

                    <h5>

                        Titular

                    </h5>

                    <p>

                        <?php
                        echo $transferencia['titular'];
                        ?>

                    </p>

                </div>

                <div class="alert alert-info mt-3">

                    Si ya realizaste tu transferencia,
                    puedes subir tu comprobante
                    de pago.

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn btn-cancel"
                    data-dismiss="modal">

                    Salir

                </button>

                <button
                    type="button"
                    class="btn btn-save"
                    id="goToFolioBtn">

                    Subir Comprobante

                </button>

            </div>

        </div>

    </div>

</div>

<!-- MODAL INGRESAR FOLIO -->

<div class="modal fade"
     id="folioModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content custom-modal">

            <div class="modal-header border-0">

                <h4 class="modal-title">

                    Ingresar Folio

                </h4>

            </div>

            <div class="modal-body">

                <label>

                    Ingresa tu folio único

                </label>

                <input
                    type="text"
                    id="folio_cliente"
                    class="form-control modern-input"
                    placeholder="CLI-XXXXXX">

            </div>

            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn btn-cancel"
                    data-dismiss="modal">

                    Cancelar

                </button>

                <button
                    type="button"
                    class="btn btn-save"
                    id="ingresarFolioBtn">

                    Ingresar

                </button>

            </div>

        </div>

    </div>

</div>

<!-- JS -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/inicio.js"></script>

</body>
</html>