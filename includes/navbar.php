<?php

$currentPage = basename($_SERVER['PHP_SELF']);

?>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <a class="navbar-brand d-flex align-items-center"
       href="pagina.php">
        <img src="img/logo_pfg-removebg-preview.png"
             class="nav-logo">
        <span class="brand-text">
            ProfitnessGym
        </span>
    </a>

    <button class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarNav">

        <i class="fas fa-bars"></i>

    </button>

    <div class="collapse navbar-collapse"
         id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'pagina.php') ? 'active-link' : ''; ?>"
                   href="pagina.php">
                    <i class="fas fa-house-user"></i>
                    Home
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   href="#"
                   
                   data-toggle="modal"
                   data-target="#addPersonModal">

                    <i class="fas fa-user-plus"></i>
                    Agregar persona
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo ($currentPage == 'personas.php' || $currentPage == 'visitantes.php') ? 'active-link' : ''; ?>" href="#" id="clientesDropdown" role="button" data-toggle="dropdown">
                    <i class="fas fa-users"></i>
                    Clientes
                </a>

                <div class="dropdown-menu">
                    <a class="dropdown-item <?php echo ($currentPage == 'personas.php') ? 'active-link' : ''; ?>"
                    href="personas.php">

                        Personas

                    </a>

                    <a class="dropdown-item <?php echo ($currentPage == 'visitantes.php') ? 'active-link' : ''; ?>"
                    href="visitantes.php">

                        Visitantes

                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   href="inactivos.php">
                    <i class="fas fa-ban"></i>
                    Inhabilitados
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   href="coaches.php">
                    <i class="fas fa-user-tie"></i>
                    Coaches
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   href="membresias.php">
                    <i class="fas fa-credit-card"></i>
                    Membresias
                </a>
            </li>

                        <li class="nav-item">
                <a class="nav-link"
                   href="productos.php">
                    <i class="fas fa-box-open"></i>
                    Productos
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    /* NAVBAR */

.navbar-custom{
    background:#ffffff;
    padding:15px 40px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

.nav-logo{
    width:55px;
    margin-right:12px;
}

.brand-text{
    font-size:24px;
    font-weight:700;
    color:#183B6B;
}

.nav-link{
    color:#6c757d !important;
    font-weight:500;
    margin-left:10px;
    transition:0.3s ease;
}

.nav-link:hover{
    color:#183B6B !important;
}

.active-link{
    color:#183B6B !important;
    font-weight:600;
}

/* BOTON HAMBURGUESA */

.navbar-toggler{
    border:none !important;
    outline:none !important;
    box-shadow:none !important;
    color:#183B6B;
    font-size:28px;
}

.navbar-toggler:focus{
    outline:none !important;
    box-shadow:none !important;
}

/* RESPONSIVE */

@media(max-width:991px){
    .navbar-custom{
        padding:15px 20px;
    }

    .navbar-nav{
        margin-top:15px;
    }

    .nav-link{
        margin-left:0;
        padding:12px 0;
    }
}

.dropdown-menu{
    border:none;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.dropdown-item{
    padding:10px 20px;
    font-weight:500;
}

.dropdown-item:hover{
    background:#f4f6f9;
    color:#183B6B;
}
</style>