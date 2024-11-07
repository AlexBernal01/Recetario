<?php
// ver_receta.php

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

// Obtener ID de la receta
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consultar la receta
$sql_receta = "SELECT * FROM recetas WHERE id = ?";
$stmt = $conn->prepare($sql_receta);
$stmt->bind_param("i", $id);
$stmt->execute();
$result_receta = $stmt->get_result();

if ($result_receta->num_rows > 0) {
    $receta = $result_receta->fetch_assoc();
    echo "<h1>" . htmlspecialchars($receta['titulo']) . "</h1>";
    echo "<p>" . htmlspecialchars($receta['descripcion']) . "</p>";
    if ($receta['imagen']) {
        echo "<img src='" . htmlspecialchars($receta['imagen']) . "' alt='" . htmlspecialchars($receta['titulo']) . "' style='max-width: 300px;'>";
    }

    // Consultar ingredientes
    $sql_ingredientes = "SELECT i.nombre, ri.cantidad FROM ingredientes i JOIN recetas_ingredientes ri ON i.id = ri.ingrediente_id WHERE ri.receta_id = ?";
    $stmt_ingredientes = $conn->prepare($sql_ingredientes);
    $stmt_ingredientes->bind_param("i", $id);
    $stmt_ingredientes->execute();
    $result_ingredientes = $stmt_ingredientes->get_result();

    echo "<h2>Ingredientes</h2>";
    echo "<ul>";
    while ($ingrediente = $result_ingredientes->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($ingrediente['nombre']) . " - " . htmlspecialchars($ingrediente['cantidad']) . "</li>";
    }
    echo "</ul>";

    $stmt_ingredientes->close();
} else {
    echo "Receta no encontrada.";
}

$stmt->close();
$conn->close();
?>
