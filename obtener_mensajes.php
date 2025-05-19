<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'mensajes_db';

try {
    // Crear conexión
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener mensajes
    $stmt = $conn->query("SELECT nombre, email, mensaje, fecha FROM mensajes ORDER BY fecha DESC");
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($mensajes);

} catch(Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?> 