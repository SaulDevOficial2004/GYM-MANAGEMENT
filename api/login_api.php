<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

$data = json_decode(file_get_contents("php://input"), true);

$telefono = $data['telefono'];
$password = $data['password'];

$sql = "
    SELECT * 
    FROM usuarios
    WHERE telefono = ?
    AND activo = 1
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("s", $telefono);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows === 1){
    
    $row = $result->fetch_assoc();

    if(password_verify($password, $row['password'])){

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['telefono'] = $row['telefono'];
        $_SESSION['nombre'] = $row['nombre'];

        echo json_encode([
            "status" => "success",

            "message" => "Bienvenido ".$row['nombre']
        ]);

    }else{
        
        echo json_encode([
            "status" => "error",

            "message" => "Contraseña incorrecta"
        ]);
    }

}else{

    echo json_encode([
        "status" => "error",

        "message" => "Usuario no encontrado"
    ]);
}

?>