<?php
session_start();
if (!isset($_SESSION['nino_id'])) {
    echo json_encode(['error' => 'Sesión no iniciada']);
    exit();
}

header('Content-Type: application/json');

// Recepción de datos vía POST
$id_categoria = $_POST['id_categoria'];
$puntos = $_POST['puntos'];

if (!isset($id_categoria) || !isset($puntos)) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "$servername"; // Cambiado 'tu_usuario' por '$servername'
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

// Preparar la consulta SQL
$sql_select = "SELECT puntos, fecha FROM progreso WHERE nino_id = ? AND id_categoria = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("ii", $_SESSION['nino_id'], $id_categoria);

if (!$stmt_select->execute()) {
    echo json_encode(['error' => 'Error en la consulta']);
    exit();
}

$result = $stmt_select->get_result();

if ($result->num_rows > 0) {
    // Existe un registro, actualizar los puntos y la fecha
    $row = $result->fetch_assoc();
    $nuevos_puntos = $row['puntos'] + $puntos;
    $sql_update = "UPDATE progreso SET puntos = ?, fecha = NOW() WHERE nino_id = ? AND id_categoria = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $nuevos_puntos, $_SESSION['nino_id'], $id_categoria);

    if (!$stmt_update->execute()) {
        echo json_encode(['error' => 'Error al actualizar los puntos']);
        exit();
    }

    echo json_encode(['success' => true]);
} else {
    // No existe un registro, crear uno nuevo
    $sql_insert = "INSERT INTO progreso (nino_id, id_categoria, puntos) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iis", $_SESSION['nino_id'], $id_categoria, $puntos);

    if (!$stmt_insert->execute()) {
        echo json_encode(['error' => 'Error al insertar el registro']);
        exit();
    }

    echo json_encode(['success' => true]);
}

$stmt_select->close();
$stmt_update->close();
$stmt_insert->close();
$conn->close();
?>