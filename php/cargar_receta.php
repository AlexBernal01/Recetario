<?php
// cargar_receta.php

$servername = "localhost"; // Cambia esto según tu configuración
$username = "root";
$password = "";
$dbname = "recetario";

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar la carga de recetas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $ingredientes = $_POST['ingredientes'];
    $cantidades = $_POST['cantidades'];

    // Manejo de la imagen
    $imagen = $_FILES['imagen']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($imagen);
    move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file);

    // Insertar la receta en la base de datos
    $sql_receta = "INSERT INTO recetas (titulo, descripcion, imagen) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_receta);
    $stmt->bind_param("sss", $titulo, $descripcion, $target_file);
    $stmt->execute();
    $receta_id = $stmt->insert_id;

    // Insertar los ingredientes en la base de datos
    foreach ($ingredientes as $index => $ingrediente) {
        $cantidad = isset($cantidades[$index]) ? $cantidades[$index] : '';
        
        // Verificar si el ingrediente ya existe en la base de datos
        $sql_ingrediente = "INSERT INTO ingredientes (nombre) VALUES (?) ON DUPLICATE KEY UPDATE id=id";
        $stmt_ingrediente = $conn->prepare($sql_ingrediente);
        $stmt_ingrediente->bind_param("s", $ingrediente);
        $stmt_ingrediente->execute();
        
        // Obtener el ID del ingrediente
        $ingrediente_id = $stmt_ingrediente->insert_id;
        
        // Insertar en la tabla intermedia
        $sql_receta_ingrediente = "INSERT INTO recetas_ingredientes (receta_id, ingrediente_id, cantidad) VALUES (?, ?, ?)";
        $stmt_receta_ingrediente = $conn->prepare($sql_receta_ingrediente);
        $stmt_receta_ingrediente->bind_param("iis", $receta_id, $ingrediente_id, $cantidad);
        $stmt_receta_ingrediente->execute();
    }

    $stmt->close();
    $conn->close();

    // Redireccionar a la página de recetas
    header("Location: mostrar_recetas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Receta</title>
</head>
<body>
    <h1>Cargar Receta</h1>
    <form action="cargar_receta.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título de la receta" required>
        <textarea name="descripcion" placeholder="Descripción de la receta" required></textarea>
        <input type="file" name="imagen" accept="image/*" required>
        <input type="text" name="ingredientes[]" placeholder="Ingrediente 1" required>
        <input type="text" name="cantidades[]" placeholder="Cantidad 1" required>
        <input type="text" name="ingredientes[]" placeholder="Ingrediente 2">
        <input type="text" name="cantidades[]" placeholder="Cantidad 2">
        <button type="submit">Cargar Receta</button>
    </form>
</body>
</html>
