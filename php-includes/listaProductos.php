<?php
// Incluir el archivo de conexión
include '../php-includes/connect.php';

// Determinar el código de actividad basado en la descripción seleccionada
$codigoActividad = isset($_GET['codigoActividad']) ? $_GET['codigoActividad'] : '464300'; // '464300' es el valor predeterminado

// Preparar la consulta SQL para obtener los códigos de producto
$sql = "SELECT codigoProducto FROM cegepafacturacion.sincronizarlistaproductosservicios WHERE codigoActividad = '$codigoActividad'";

// Ejecutar la consulta
$result = mysqli_query($con, $sql);

// Verificar si la consulta fue exitosa
if($result){
    $codigosProductos = [];
    while($row = mysqli_fetch_assoc($result)){
        $codigosProductos[] = $row['codigoProducto'];
    }
    // Devolver los resultados en formato JSON
    echo json_encode(['codigosProductos' => $codigosProductos]);
} else {
    echo json_encode(['error' => 'Error al realizar la consulta.']);
}

// Cerrar la conexión
mysqli_close($con);
?>


