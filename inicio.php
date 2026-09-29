<?php
require_once __DIR__.'/php_action/conn_db.php';

$sqlMembresias="
    SELECT
        id,
        nombre,
        descripcion,
        precio,
        promocion,
        precio_promocion
    FROM membresias
    WHERE activo=1
    ORDER BY precio ASC
";

$resultMembresias=$connect->query($sqlMembresias);

$sqlTransferencia="
    SELECT
        banco,
        clabe,
        titular
    FROM configuracion_transferencias
    LIMIT 1
";

$resultTransferencia=$connect->query($sqlTransferencia);

$transferencia=$resultTransferencia
    ?$resultTransferencia->fetch_assoc()
    :null;

$totalPlanes=$resultMembresias
    ?$resultMembresias->num_rows
    :0;
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>GYM MANAGEMENT</title>
    <?php include 'includes/head.php'; ?>

    <!-- CSS -->

    <link rel="stylesheet"
          href="css/inicio.css">

</head>

<body>

<!-- ===================== -->
<!-- NAVBAR -->
<!-- ===================== -->

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand gym-brand" href="#inicio">
            <span class="gym-brand-logo">
                <img src="img/logo.png" alt="GYM MANAGEMENT">
            </span>
            <span class="gym-brand-text">
                <strong>GYM MANAGEMENT</strong>
                <small>Entrena. Progresa. Supera.</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Abrir navegación">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="#inicio" class="nav-link">Inicio</a>
                </li>
                <li class="nav-item">
                    <a href="#beneficios" class="nav-link">Beneficios</a>
                </li>
                <li class="nav-item">
                    <a href="#horarios" class="nav-link">Horarios</a>
                </li>
                <li class="nav-item">
                    <a href="#planes" class="nav-link">Planes</a>
                </li>
                <li class="nav-item">
                    <a href="#ubicacion" class="nav-link">Ubicación</a>
                </li>
                <li class="nav-item">
                    <a href="auto_busqueda.php" class="nav-membership-button">
                        <i class="fas fa-id-card"></i>
                        Mi membresía
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php" class="nav-admin-button" title="Acceso administrativo">
                        <i class="fas fa-lock"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main>
    <section id="inicio" class="public-hero">
        <div class="public-hero-background"></div>
        <div class="container public-hero-container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="public-hero-content">
                        <span class="public-hero-badge">
                            <i class="fas fa-dumbbell"></i>
                            Tu cambio comienza aquí
                        </span>

                        <h1>
                            Construye una versión
                            <span>más fuerte de ti.</span>
                        </h1>

                        <p>Entrena en un espacio preparado para ayudarte a mejorar tu fuerza, condición física y disciplina todos los días.</p>

                        <div class="public-hero-actions">
                            <a href="#planes" class="public-primary-button">
                                <i class="fas fa-fire"></i>
                                Ver planes
                            </a>

                            <a href="auto_busqueda.php" class="public-secondary-button">
                                <i class="fas fa-id-card"></i>
                                Consultar membresía
                            </a>
                        </div>

                        <div class="public-hero-features">
                            <span>
                                <i class="fas fa-check-circle"></i>
                                Área de pesas
                            </span>
                            <span>
                                <i class="fas fa-check-circle"></i>
                                Área de cardio
                            </span>
                            <span>
                                <i class="fas fa-check-circle"></i>
                                Horarios amplios
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <aside class="public-access-card">
                        <div class="public-access-header">
                            <span>
                                <i class="fas fa-mobile-alt"></i>
                            </span>
                            <div>
                                <small>Servicios digitales</small>
                                <h2>Tu gimnasio también está en línea</h2>
                            </div>
                        </div>

                        <p>Consulta el estado de tu membresía o identifica tu pago por transferencia usando tu folio personal.</p>

                        <a href="auto_busqueda.php" class="public-service-link">
                            <span>
                                <i class="fas fa-id-card"></i>
                            </span>
                            <div>
                                <small>Consulta personal</small>
                                <strong>Ver mi membresía</strong>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>

                        <button type="button" class="public-service-link public-service-button" data-toggle="modal" data-target="#transferenciaModal">
                            <span>
                                <i class="fas fa-money-check-alt"></i>
                            </span>
                            <div>
                                <small>Pago bancario</small>
                                <strong>Pagar por transferencia</strong>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <div class="public-access-security">
                            <i class="fas fa-shield-alt"></i>
                            Tus datos se consultan mediante un folio único.
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section id="beneficios" class="benefits-section">
        <div class="container">
            <div class="public-section-heading">
                <span>Todo lo que necesitas</span>
                <h2>Un espacio creado para tu progreso</h2>
                <p>Entrena con instalaciones, servicios y herramientas pensadas para acompañar tu desarrollo.</p>
            </div>

            <div class="benefits-grid">
                <article class="benefit-card">
                    <span class="benefit-icon">
                        <i class="fas fa-dumbbell"></i>
                    </span>
                    <h3>Área de pesas</h3>
                    <p>Equipo para desarrollar fuerza, masa muscular y resistencia.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-icon">
                        <i class="fas fa-heart-pulse"></i>
                    </span>
                    <h3>Área de cardio</h3>
                    <p>Mejora tu condición física y complementa tu entrenamiento.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-icon">
                        <i class="fas fa-calendar-check"></i>
                    </span>
                    <h3>Planes flexibles</h3>
                    <p>Elige la membresía que mejor se adapte a tus objetivos.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-icon">
                        <i class="fas fa-mobile-screen-button"></i>
                    </span>
                    <h3>Control digital</h3>
                    <p>Consulta tu membresía y envía tus comprobantes en línea.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="horarios" class="schedule-section">
        <div class="container">
            <div class="schedule-layout">
                <div class="schedule-introduction">
                    <span class="public-section-label">Horarios de atención</span>
                    <h2>Entrena en el momento que mejor se adapte a ti.</h2>
                    <p>Contamos con horarios amplios para que puedas mantener tu rutina sin importar tus actividades diarias.</p>

                    <a href="#ubicacion" class="schedule-location-link">
                        <i class="fas fa-location-dot"></i>
                        Consultar ubicación
                    </a>
                </div>

                <div class="schedule-list">
                    <article class="schedule-row">
                        <span class="schedule-day-icon">
                            <i class="fas fa-calendar-week"></i>
                        </span>
                        <div>
                            <strong>Lunes a viernes</strong>
                            <small>Horario regular</small>
                        </div>
                        <time>6:00 AM – 10:00 PM</time>
                    </article>

                    <article class="schedule-row">
                        <span class="schedule-day-icon">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                        <div>
                            <strong>Sábado</strong>
                            <small>Horario especial</small>
                        </div>
                        <time>7:00 AM – 3:00 PM</time>
                    </article>

                    <article class="schedule-row schedule-closed">
                        <span class="schedule-day-icon">
                            <i class="fas fa-calendar-xmark"></i>
                        </span>
                        <div>
                            <strong>Domingo</strong>
                            <small>Descanso semanal</small>
                        </div>
                        <time>Cerrado</time>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="public-numbers-section">
        <div class="container">
            <div class="public-numbers-card">
                <article>
                    <span>
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <div>
                        <strong class="counter" data-target="<?php echo $totalPlanes; ?>">0</strong>
                        <small>Planes disponibles</small>
                    </div>
                </article>

                <article>
                    <span>
                        <i class="fas fa-clock"></i>
                    </span>
                    <div>
                        <strong>16</strong>
                        <small>Horas disponibles entre semana</small>
                    </div>
                </article>

                <article>
                    <span>
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <div>
                        <strong>6</strong>
                        <small>Días de atención</small>
                    </div>
                </article>

                <article>
                    <span>
                        <i class="fas fa-shield-alt"></i>
                    </span>
                    <div>
                        <strong>100%</strong>
                        <small>Control digital de membresías</small>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="planes" class="plans-section">
        <div class="container">
            <div class="public-section-heading">
                <span>Membresías</span>
                <h2>Elige tu plan de entrenamiento</h2>
                <p>Consulta las opciones disponibles y selecciona la membresía que mejor se adapte a ti.</p>
            </div>

            <div class="row justify-content-center">
                <?php if($resultMembresias&&$resultMembresias->num_rows>0): ?>
                    <?php while($membresia=$resultMembresias->fetch_assoc()): ?>
                        <?php
                        $esPromocion=(int)$membresia['promocion']===1;
                        $nombrePlan=htmlspecialchars($membresia['nombre'],ENT_QUOTES,'UTF-8');
                        $descripcionPlan=htmlspecialchars($membresia['descripcion']??'',ENT_QUOTES,'UTF-8');
                        $precioNormal=(float)$membresia['precio'];
                        $precioFinal=$esPromocion
                            ?(float)$membresia['precio_promocion']
                            :$precioNormal;
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <article class="public-plan-card <?php echo $esPromocion?'public-plan-featured':''; ?>">
                                <?php if($esPromocion): ?>
                                    <span class="public-plan-badge">
                                        <i class="fas fa-fire"></i>
                                        Promoción
                                    </span>
                                <?php endif; ?>

                                <div class="public-plan-icon">
                                    <i class="fas fa-dumbbell"></i>
                                </div>

                                <h3><?php echo $nombrePlan; ?></h3>
                                <p><?php echo $descripcionPlan!==''?$descripcionPlan:'Plan de entrenamiento disponible.'; ?></p>

                                <div class="public-plan-price">
                                    <?php if($esPromocion): ?>
                                        <small>$<?php echo number_format($precioNormal,2); ?></small>
                                    <?php endif; ?>

                                    <strong>
                                        <span>$</span>
                                        <?php echo number_format($precioFinal,2); ?>
                                    </strong>
                                </div>

                                <ul>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        Acceso según vigencia del plan
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        Consulta digital de membresía
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        Pago mediante transferencia
                                    </li>
                                </ul>

                                <button type="button" class="public-plan-button" data-toggle="modal" data-target="#transferenciaModal">
                                    Seleccionar plan
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </article>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="public-empty-plans">
                            <i class="fas fa-circle-info"></i>
                            <h3>No hay planes disponibles</h3>
                            <p>Consulta directamente en recepción para conocer las membresías actuales.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="ubicacion" class="location-section">
        <div class="container">
            <div class="location-card">
                <div class="location-map">
                    <div class="location-map-placeholder">
                        <span>
                            <i class="fas fa-map-location-dot"></i>
                        </span>
                        <h3>Ubicación del gimnasio</h3>
                        <p>Agrega aquí el enlace de Google Maps cuando tengas la dirección definitiva.</p>
                    </div>
                </div>

                <div class="location-information">
                    <span class="public-section-label">Visítanos</span>
                    <h2>Estamos en Zihuatanejo, Guerrero.</h2>
                    <p>Conoce nuestras instalaciones, consulta los planes disponibles y comienza tu entrenamiento.</p>

                    <div class="location-detail">
                        <span>
                            <i class="fas fa-location-dot"></i>
                        </span>
                        <div>
                            <small>Dirección</small>
                            <strong>Zihuatanejo, Guerrero, México</strong>
                        </div>
                    </div>

                    <div class="location-detail">
                        <span>
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <small>Horario</small>
                            <strong>Lunes a viernes, 6:00 AM – 10:00 PM</strong>
                        </div>
                    </div>

                    <a href="#" class="public-primary-button location-route-button">
                        <i class="fas fa-route"></i>
                        Cómo llegar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="public-cta-section">
        <div class="container">
            <div class="public-cta-card">
                <div>
                    <span>Comienza hoy</span>
                    <h2>Tu progreso empieza con una decisión.</h2>
                    <p>Consulta nuestros planes y encuentra la opción adecuada para comenzar a entrenar.</p>
                </div>

                <div class="public-cta-actions">
                    <a href="#planes" class="public-primary-button">
                        Ver planes
                        <i class="fas fa-arrow-right"></i>
                    </a>

                    <a href="auto_busqueda.php" class="public-secondary-button public-dark-secondary">
                        Consultar membresía
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="public-footer">
    <div class="container">
        <div class="public-footer-grid">
            <div class="public-footer-brand">
                <div>
                    <img src="img/logo.png" alt="GYM MANAGEMENT">
                </div>
                <h3>GYM MANAGEMENT</h3>
                <p>Un espacio para construir fuerza, disciplina y una mejor versión de ti.</p>
            </div>

            <div class="public-footer-links">
                <h4>Navegación</h4>
                <a href="#inicio">Inicio</a>
                <a href="#beneficios">Beneficios</a>
                <a href="#planes">Planes</a>
                <a href="#ubicacion">Ubicación</a>
            </div>

            <div class="public-footer-links">
                <h4>Servicios</h4>
                <a href="auto_busqueda.php">Consultar membresía</a>
                <button type="button" data-toggle="modal" data-target="#transferenciaModal">Pagar por transferencia</button>
                <a href="index.php">Acceso administrativo</a>
            </div>

            <div class="public-footer-contact">
                <h4>Contacto</h4>
                <p>
                    <i class="fas fa-location-dot"></i>
                    Zihuatanejo, Guerrero
                </p>

                <div class="public-social-links">
                    <a href="https://www.facebook.com/profile.php?id=100042239455651" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/gym.pro_fitness" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="public-footer-bottom">
            <span>© <?php echo date('Y'); ?> GYM MANAGEMENT. Todos los derechos reservados.</span>
            <span>
                <i class="fas fa-shield-alt"></i>
                Sistema de gestión y membresías
            </span>
        </div>
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

                <div class="bank-card">
                    <?php if($transferencia): ?>
                        <h5>Banco</h5>
                        <p>
                            <?php echo htmlspecialchars(
                                $transferencia['banco']??'No configurado',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </p>

                        <h5>CLABE</h5>
                        <p>
                            <?php echo htmlspecialchars(
                                $transferencia['clabe']??'No configurada',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </p>

                        <h5>Titular</h5>
                        <p>
                            <?php echo htmlspecialchars(
                                $transferencia['titular']??'No configurado',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </p>
                    <?php else: ?>
                        <div class="bank-empty">
                            <i class="fas fa-circle-info"></i>
                            Los datos bancarios no están disponibles.
                        </div>
                    <?php endif; ?>
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
                    placeholder="CLI-XXXXXX"
                    maxlength="10"
                    autocomplete="off"
                    spellcheck="false">

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
                    <i class="fas fa-arrow-right mr-2"></i>
                    Ingresar
                </button>

            </div>

        </div>

    </div>

</div>

<!-- JS -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>

<script src="js/inicio.js"></script>


<?php
$connect->close();
?>
</body>
</html>