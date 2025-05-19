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

    // Verificar si la tabla existe, si no, crearla
    $conn->exec("CREATE TABLE IF NOT EXISTS mensajes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        mensaje TEXT NOT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Obtener y validar datos
    $nombre = htmlspecialchars($_POST['nombre'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $mensaje = htmlspecialchars($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Insertar mensaje
    $stmt = $conn->prepare("INSERT INTO mensajes (nombre, email, mensaje) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $email, $mensaje]);

    echo json_encode(['success' => true]);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 