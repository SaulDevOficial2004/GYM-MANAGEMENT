<?php

session_start();

if(!isset($_SESSION['telefono'])){

    header("Location: index.php");
    exit();

}

require_once 'php_action/conn_db.php';

/* TOTAL INHABILITADOS */

$sqlTotal = "
  SELECT COUNT(*) AS personas
  FROM personas
  WHERE estatus = 2
";

$resultTotal = $connect->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();
$total_personas = $rowTotal['personas'];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>Inhabilitados | ProfitnessGym</title>

    <?php include 'includes/head.php' ?>
    <!-- CSS -->
    <link rel="stylesheet" href="css/inactivos.css">

</head>

<body>

<?php include 'includes/navbar.php' ?>

<!-- CONTENIDO -->

<div class="content-card">

    <!-- HEADER -->

    <div class="header-toolbar">

        <div>

            <h2>

                <i class="fas fa-user-slash"></i>

                Personas Inhabilitadas

            </h2>

            <p>

                Total de personas inhabilitadas:

                <strong>

                    <?php echo $total_personas; ?>

                </strong>

            </p>

        </div>

    </div>

    <!-- BUSCADOR -->

    <div class="search-box">

        <i class="fas fa-search"></i>

        <input
            type="text"
            id="searchInactive"
            placeholder="Buscar persona por nombre o folio">

    </div>

    <!-- TABLA -->

    <div class="table-responsive">

        <table
            id="inactiveTable"
            class="table modern-table">

            <thead>

                <tr>

                    <th>Nombre</th>

                    <th>Folio</th>

                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $sql = "

            SELECT *

            FROM personas

            WHERE estatus = 2

            ORDER BY nombre ASC

            ";

            $result = $connect->query($sql);

            if($result->num_rows > 0){

                while($row = $result->fetch_assoc()){

                    echo "

                    <tr>

                        <td>

                            {$row['nombre']}

                        </td>

                        <td>

                            {$row['folio']}

                        </td>

                        <td>

                            <button

                            type='button'

                            class='btn-enable enableBtn'

                            data-id='{$row['id']}'

                            data-nombre=\"{$row['nombre']}\">

                                <i class='fas fa-check'></i>

                                Habilitar

                            </button>

                        </td>

                    </tr>

                    ";

                }

            }else{

                echo "

                <tr>

                    <td

                    colspan='3'

                    class='empty-state text-center'>

                        <i class='fas fa-check-circle'></i>

                        <br><br>

                        No hay personas inhabilitadas

                    </td>

                </tr>

                ";

            }

            ?>

            </tbody>

        </table>

    </div>

</div>

<?php include 'includes/modals/add_person_modal.php' ?>

<?php include 'includes/footer.php' ?>

<!-- JS -->

<script src="js/inactivos.js"></script>
<script src="js/personas/add_person.js"></script>

</body>

</html>

<?php

$connect->close();

?>