<?php
//Configuramos la conexion a la base de datos
$localhost = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "profitnessgym";

//Hacemos el pase de caracteres en formato utf8 para la codificacion de los mismos

$connect = new mysqli($localhost, $username, $password, $dbname);

mysqli_set_charset($connect, 'utf8');

//Verificamos la conexion
if($connect->connect_error){
    die("La conexion fallo: ".$connect->connect_error);
}else{
    //echo "Conectado con exito";
}

?>