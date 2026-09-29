<?php

$currentPage = basename($_SERVER['PHP_SELF']);

$showAddPerson = in_array($currentPage,
    [
        'pagina.php',
        'personas.php'
    ],
    true
);

?>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-custom">

    <!-- MARCA -->

    <a class="navbar-brand"
        href="pagina.php">

        <img
            src="img/logo.png"
            class="nav-logo"
            alt="GMS">

        <span class="brand-information">

            <strong class="brand-acronym">

                GMS

            </strong>

            <small class="brand-full-name">

                GYM MANAGEMENT SYSTEM

            </small>

        </span>

    </a>

    <!-- BOTÓN MÓVIL -->

    <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Abrir menú">

        <i class="fas fa-bars"></i>

    </button>

    <!-- NAVEGACIÓN -->

    <div
        class="collapse navbar-collapse"
        id="navbarNav">

        <ul class="navbar-nav ml-auto">

            <!-- DASHBOARD -->

            <li class="nav-item">

                <a
                    class="nav-link <?php echo (
                        $currentPage === 'pagina.php'
                    ) ? 'active-link' : ''; ?>"
                    href="pagina.php">

                    <i class="fas fa-house-user"></i>

                    <span>
                        Inicio
                    </span>

                </a>

            </li>

            <!-- AGREGAR PERSONA -->

            <?php if($showAddPerson): ?>

                <li class="nav-item">

                    <a
                        class="nav-link nav-link-add-person"
                        href="#"
                        data-toggle="modal"
                        data-target="#addPersonModal">

                        <i class="fas fa-user-plus"></i>

                        <span>
                            Agregar persona
                        </span>

                    </a>

                </li>

            <?php endif; ?>

            <!-- CLIENTES -->

            <li class="nav-item dropdown">

                <a
                    class="nav-link dropdown-toggle <?php echo (
                        $currentPage === 'personas.php'
                        || $currentPage === 'visitantes.php'
                    ) ? 'active-link' : ''; ?>"
                    href="#"
                    id="clientesDropdown"
                    role="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">

                    <i class="fas fa-users"></i>

                    <span>
                        Clientes
                    </span>

                </a>

                <div
                    class="dropdown-menu"
                    aria-labelledby="clientesDropdown">

                    <a
                        class="dropdown-item <?php echo (
                            $currentPage === 'personas.php'
                        ) ? 'active-dropdown-item' : ''; ?>"
                        href="personas.php">

                        <i class="fas fa-address-card"></i>

                        <span>
                            Personas
                        </span>

                    </a>

                    <a
                        class="dropdown-item <?php echo (
                            $currentPage === 'visitantes.php'
                        ) ? 'active-dropdown-item' : ''; ?>"
                        href="visitantes.php">

                        <i class="fas fa-person-walking"></i>

                        <span>
                            Visitantes
                        </span>

                    </a>

                </div>

            </li>

            <!-- RECEPCIONISTAS -->

            <?php if(
                isRole('Administrador')
                || isRole('Dueño')
            ): ?>

                <li class="nav-item">

                    <a
                        class="nav-link <?php echo (
                            $currentPage === 'recepcionistas.php'
                        ) ? 'active-link' : ''; ?>"
                        href="recepcionistas.php">

                        <i class="fas fa-user-tie"></i>

                        <span>
                            Recepcionistas
                        </span>

                    </a>

                </li>

            <?php endif; ?>

            <!-- MEMBRESÍAS -->

            <?php if(
                isRole('Administrador')
                || isRole('Dueño')
            ): ?>

                <li class="nav-item">

                    <a
                        class="nav-link <?php echo (
                            $currentPage === 'membresias.php'
                        ) ? 'active-link' : ''; ?>"
                        href="membresias.php">

                        <i class="fas fa-credit-card"></i>

                        <span>
                            Membresías
                        </span>

                    </a>

                </li>

            <?php endif; ?>

            <!-- PRODUCTOS -->

            <li class="nav-item">

                <a
                    class="nav-link <?php echo (
                        $currentPage === 'productos.php'
                    ) ? 'active-link' : ''; ?>"
                    href="productos.php">

                    <i class="fas fa-box-open"></i>

                    <span>
                        Productos
                    </span>

                </a>

            </li>

        </ul>

    </div>

</nav>

<style>

/* ===================================== */
/* NAVBAR */
/* ===================================== */

.navbar-custom{
    position:relative;
    z-index:1000;
    min-height:78px;
    padding:12px 32px;
    font-family:'Poppins',sans-serif;
    background:#ffffff;
    box-shadow:
        0 5px 20px
        rgba(0,0,0,.05);

}


/* ===================================== */
/* MARCA */
/* ===================================== */

.navbar-brand{
    display:inline-flex;
    align-items:center;
    gap:10px;
    min-width:max-content;
    margin-right:24px;
    padding:0;
}

.nav-logo{
    display:block;
    width:48px;
    height:48px;
    object-fit:contain;
    flex-shrink:0;
}

.brand-information{
    display:flex;
    flex-direction:column;
    justify-content:center;
    line-height:1.05;
}

.brand-acronym{
    color:#183B6B;
    font-size:25px;
    font-weight:700;
    letter-spacing:1px;
}

.brand-full-name{
    margin-top:4px;
    color:#6c757d;
    font-size:9px;
    font-weight:600;
    letter-spacing:.8px;
    white-space:nowrap;
}


/* ===================================== */
/* NAVEGACIÓN */
/* ===================================== */

.navbar-nav{
    align-items:center;
    gap:3px;
}

.nav-item{
    display:flex;
    align-items:center;
}

.nav-link{
    position:relative;
    display:flex !important;
    align-items:center;
    justify-content:flex-start;
    gap:8px;
    min-height:44px;
    margin:0 !important;
    padding:10px 12px !important;
    border-radius:11px;
    color:#6c757d !important;
    font-size:14px;
    font-weight:500;
    line-height:1;
    white-space:nowrap;
    transition:
        color .2s ease,
        background .2s ease,
        transform .2s ease;
}

.nav-link i{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:19px;
    min-width:19px;
    height:19px;
    font-size:16px;
    line-height:1;
    flex-shrink:0;
}

.nav-link span{
    display:inline-flex;
    align-items:center;
    line-height:1;
}

.nav-link:hover{
    color:#183B6B !important;
    background:#f4f7fc;
    transform:translateY(-1px);
}

.active-link{
    color:#183B6B !important;
    background:#edf4ff;
    font-weight:600;

}

.active-link::after{
    content:"";
    position:absolute;
    left:12px;
    right:12px;
    bottom:4px;
    height:2px;
    border-radius:10px;
    background:#16BFFD;
}

.nav-link-add-person{
    color:#183B6B !important;
}


/* ===================================== */
/* DROPDOWN */
/* ===================================== */

.dropdown-menu{
    min-width:190px;
    margin-top:8px;
    padding:8px;
    border:none;
    border-radius:15px;
    background:#ffffff;
    box-shadow:
        0 12px 30px
        rgba(24,59,107,.12);
}

.dropdown-item{
    display:flex;
    align-items:center;
    gap:10px;
    min-height:42px;
    padding:10px 12px;
    border-radius:10px;
    color:#6c757d;
    font-size:14px;
    font-weight:500;
    transition:.2s;
}

.dropdown-item i{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:18px;
    min-width:18px;
    height:18px;
    color:#183B6B;
    flex-shrink:0;
}

.dropdown-item:hover{
    background:#f4f7fc;
    color:#183B6B;
}

.active-dropdown-item{
    background:#edf4ff;
    color:#183B6B;
    font-weight:600;
}


/* ===================================== */
/* BOTÓN HAMBURGUESA */
/* ===================================== */

.navbar-toggler{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:45px;
    height:45px;
    padding:0;
    border:none !important;
    border-radius:12px;
    background:#f4f7fc;
    color:#183B6B;
    font-size:22px;
    outline:none !important;
    box-shadow:none !important;
}

.navbar-toggler:focus,
.navbar-toggler:hover{
    border:none !important;
    background:#edf4ff;
    color:#16BFFD;
    outline:none !important;
    box-shadow:none !important;
}


/* ===================================== */
/* TABLET Y MÓVIL */
/* ===================================== */

@media(max-width:1199px){
    .navbar-custom{
        padding-left:20px;
        padding-right:20px;
    }

    .nav-link{
        padding-left:9px !important;
        padding-right:9px !important;
        font-size:13px;
    }
}


@media(max-width:991px){
    .navbar-custom{
        min-height:70px;
        padding:10px 16px;
    }

    .navbar-brand{
        margin-right:0;
    }

    .nav-logo{
        width:43px;
        height:43px;
    }

    .brand-acronym{
        font-size:23px;
    }

    .brand-full-name{
        font-size:8px;
    }

    .navbar-collapse{
        margin-top:12px;
        padding:12px;
        border-radius:15px;
        background:#ffffff;
        box-shadow:
            0 12px 30px
            rgba(24,59,107,.10);
    }

    .navbar-nav{
        align-items:stretch;
        gap:4px;
        margin-top:0;
    }

    .nav-item{
        display:block;
        width:100%;
    }

    .nav-link{
        width:100%;
        min-height:47px;
        padding:12px 14px !important;
        font-size:14px;
    }

    .nav-link:hover{
        transform:none;
    }

    .active-link::after{
        top:9px;
        bottom:9px;
        left:4px;
        right:auto;
        width:3px;
        height:auto;
    }

    .dropdown-menu{
        position:static !important;
        float:none;
        width:100%;
        margin:4px 0 7px;
        padding:5px 5px 5px 18px;
        border-radius:12px;
        background:#f8fafc;
        box-shadow:none;
        transform:none !important;
    }

    .dropdown-item{
        width:100%;
    }
}


@media(max-width:420px){
    .navbar-custom{
        padding-left:12px;
        padding-right:12px;
    }

    .brand-full-name{
        display:none;
    }

    .brand-information{
        justify-content:center;
    }

    .brand-acronym{
        font-size:24px;
    }

    .nav-logo{
        width:41px;
        height:41px;
    }
}

</style>