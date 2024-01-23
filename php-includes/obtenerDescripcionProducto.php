<?php
// Incluir el archivo de conexión
include './connect.php';

// Usa filter_input para una validación/sanitización más segura de las entradas
$codigoActividad = filter_input(INPUT_GET, 'codigoActividad', FILTER_SANITIZE_STRING);
$codigoProducto = filter_input(INPUT_GET, 'codigoProducto', FILTER_SANITIZE_STRING);

// Preparar la consulta SQL
$sql = "SELECT descripcionProducto FROM sincronizarlistaproductosservicios WHERE codigoActividad = ? AND codigoProducto = ?;";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    // Manejo de error en la preparación de la consulta
    echo 'Error en la preparación de la consulta.';
    exit;
}

$stmt->bind_param('ss', $codigoActividad, $codigoProducto); // 'ss' porque ambos son cadenas (strings)
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    // Imprimir la descripción del producto como texto plano
    echo $row['descripcionProducto'];
} else {
    // Imprimir un mensaje de error si no se encuentra la descripción
    echo 'No se encontró la descripción.';
}

$stmt->close();
$con->close();
?>
