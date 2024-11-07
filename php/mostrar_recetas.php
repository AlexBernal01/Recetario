<?php
// mostrar_recetas.php

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

// Consultar recetas
$sql = "SELECT * FROM recetas";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mostrar Recetas</title>
</head>
<body>
    <h1>Recetas Disponibles</h1>

    <?php
    if ($result->num_rows > 0) {
        echo "<div class='recetas'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='receta'>";
            echo "<h3>" . htmlspecialchars($row['titulo']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['descripcion']) . "</p>";
            if ($row['imagen']) {
                echo "<img src='" . htmlspecialchars($row['imagen']) . "' alt='" . htmlspecialchars($row['titulo']) . "' style='max-width: 200px;'>";
            }
            echo "<a href='ver_receta.php?id=" . $row['id'] . "'>Ver Detalles</a>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "No hay recetas disponibles.";
    }

    $conn->close();
    ?>
</body>
</html>
