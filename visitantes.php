<?php

session_start();

if(!isset($_SESSION['telefono'])){

    header('location:index.php');
    exit();

}

require_once 'php_action/conn_db.php';

$sql = "
    SELECT
        visitantes.id,
        visitantes.nombre,
        visitas.fecha_visita
    FROM visitas
    INNER JOIN visitantes
    ON visitantes.id = visitas.visitante_id
    ORDER BY visitas.fecha_visita DESC
";

$result = $connect->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>
        Visitantes | ProfitnessGym
    </title>

    <?php include 'includes/head.php'; ?>

    <link rel="stylesheet"
          href="css/visitantes.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>

<!-- CONTENIDO -->

<div class="container-fluid visitantes-container">

    <!-- HEADER -->

    <div class="page-header">

        <h1>

            Historial de Visitantes

        </h1>

        <p>

            Registro de visitas agrupado por fecha

        </p>

    </div>

    <!-- BUSCADOR -->

    <div class="visitas-toolbar">

        <input
            type="text"
            id="searchVisitantes"
            class="search-input"
            placeholder="Buscar visitante...">

    </div>

    <!-- HISTORIAL -->

    <div id="visitasContainer">

        <?php

        $fecha_actual = '';

        while($row = $result->fetch_assoc()){

            $fecha = date(
                "d/m/Y",
                strtotime($row['fecha_visita'])
            );

            if($fecha_actual != $fecha){

                if($fecha_actual != ''){

                    echo '</div></div>';

                }

                echo '

                <div class="visit-date-group">

                    <div class="visit-date">

                        '.$fecha.'

                    </div>

                    <div class="visit-list">

                ';

                $fecha_actual = $fecha;

            }

                echo '

                <div class="visitante-card">

                    <div class="visitante-info">

                        <div class="visitante-nombre">

                            <i class="fas fa-user"></i>

                            '.$row['nombre'].'

                        </div>

                        <div class="visitante-fecha">

                            '.date("d/m/Y H:i",strtotime($row['fecha_visita'])).'

                        </div>

                    </div>

                    <button
                        class="btn-register-visit"
                        data-id="'.$row['id'].'">

                        <i class="fas fa-plus"></i>

                        Registrar visita

                    </button>

                </div>

                ';

        }

        if($fecha_actual != ''){

            echo '</div></div>';

        }

        ?>

    </div>

</div>

<?php include 'includes/modals/add_person_modal.php' ?>

<?php include 'includes/footer.php'; ?>

<script src="js/visitantes.js"></script>
<script src="js/personas/add_person.js"></script>

</body>
</html>

<?php

$connect->close();

?>