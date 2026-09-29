<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="description" content="Sistema administrativo para la gestión integral de gimnasios.">
    <title>GYM MANAGEMENT | Iniciar sesión</title>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="css/login.css">
</head>
<body<?php $flashes=[]; if(isset($_GET['error'])){ $flashes[]='loginError'; } if(isset($_GET['success'])){ $flashes[]='loginSuccess'; } if($flashes!==[]){ echo ' data-flash="'.implode(',',$flashes).'"'; } ?>>
<main class="login-page">
    <section class="login-shell">
        <div class="login-panel">
            <div class="login-panel-content">
                <div class="login-brand">
                    <div class="login-logo-container">
                        <img src="img/logo.png" class="login-logo" alt="GYM MANAGEMENT">
                    </div>
                    <div>
                        <span class="login-brand-label">Sistema administrativo</span>
                        <strong>GYM MANAGEMENT</strong>
                    </div>
                </div>

                <div class="login-heading">
                    <span class="login-eyebrow">Acceso seguro</span>
                    <h1>Iniciar sesión</h1>
                    <p>Ingresa tus credenciales para acceder al panel administrativo del gimnasio.</p>
                </div>

                <form id="loginForm" method="POST" novalidate>
                    <div class="login-form-group">
                        <label for="telefono">Número de teléfono</label>
                        <div class="login-input-wrapper">
                            <span class="login-input-icon">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" id="telefono" name="telefono" class="login-input" placeholder="Ingresa tu número de teléfono" autocomplete="username" inputmode="numeric" maxlength="20">
                        </div>
                        <small class="login-field-message" id="telefonoMessage"></small>
                    </div>

                    <div class="login-form-group">
                        <div class="login-label-row">
                            <label for="password">Contraseña</label>
                            <span>
                                <i class="fas fa-shield-alt"></i>
                                Acceso protegido
                            </span>
                        </div>
                        <div class="login-input-wrapper">
                            <span class="login-input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password" class="login-input login-password-input" placeholder="Ingresa tu contraseña" autocomplete="current-password">
                            <button type="button" id="togglePassword" class="login-password-toggle" aria-label="Mostrar contraseña">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="login-field-message" id="passwordMessage"></small>
                    </div>

                    <button type="submit" class="login-submit-button" id="loginSubmitButton">
                        <span class="login-submit-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </span>
                        <span class="login-submit-text">Ingresar al sistema</span>
                        <i class="fas fa-arrow-right login-submit-arrow"></i>
                    </button>
                </form>

                <div class="login-assistance">
                    <span>
                        <i class="fas fa-circle-info"></i>
                    </span>
                    <p>El acceso está disponible únicamente para usuarios autorizados del gimnasio.</p>
                </div>

                <div class="login-mobile-status">
                    <span>¿Eres cliente?</span>
                    <a href="auto_busqueda.php">
                        Consultar mi estatus
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <footer class="login-panel-footer">
                <span>GYM MANAGEMENT</span>
                <small>Plataforma de administración</small>
            </footer>
        </div>

        <aside class="login-presentation">
            <div class="presentation-decoration decoration-one"></div>
            <div class="presentation-decoration decoration-two"></div>
            <div class="presentation-grid"></div>

            <div class="presentation-content">
                <span class="presentation-badge">
                    <i class="fas fa-dumbbell"></i>
                    Gestión integral
                </span>

                <h2>Administra tu gimnasio desde un solo lugar.</h2>

                <p>Controla clientes, membresías, ventas, pagos y operaciones mediante una plataforma organizada, moderna y segura.</p>

                <div class="presentation-features">
                    <article>
                        <span>
                            <i class="fas fa-users"></i>
                        </span>
                        <div>
                            <strong>Control de clientes</strong>
                            <small>Información y membresías centralizadas.</small>
                        </div>
                    </article>

                    <article>
                        <span>
                            <i class="fas fa-chart-line"></i>
                        </span>
                        <div>
                            <strong>Reportes financieros</strong>
                            <small>Consulta ingresos y rendimiento del negocio.</small>
                        </div>
                    </article>

                    <article>
                        <span>
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <div>
                            <strong>Accesos por roles</strong>
                            <small>Protección para cada nivel administrativo.</small>
                        </div>
                    </article>
                </div>

                <a href="auto_busqueda.php" class="presentation-status-button">
                    <span>
                        <i class="fas fa-user-check"></i>
                    </span>
                    <div>
                        <small>Acceso para clientes</small>
                        <strong>Consultar mi estatus</strong>
                    </div>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="presentation-footer">
                <span>
                    <i class="fas fa-lock"></i>
                    Plataforma privada
                </span>
                <span>GYM MANAGEMENT © 2026</span>
            </div>
        </aside>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.js"
        integrity="sha384-VwoO4KYHycI5E2Vzjf4m+IY3C8JKnLhRzzVeR7n4Qdx+Qkq0YUC3aiJ6vY0XVlVT"
        crossorigin="anonymous"></script>
<script src="js/alerts.js"></script>
<script src="js/login.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>
</body>
</html>