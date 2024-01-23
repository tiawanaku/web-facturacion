<?php
// Incluir el archivo de conexión
include '../php-includes/connect.php';

// Preparar la consulta SQL para obtener las unidades de medida
$sql = "SELECT descripcion FROM cegepafacturacion.sincronizarparametricaunidadmedida";

// Ejecutar la consulta
$result = mysqli_query($con, $sql);

// Verificar si la consulta fue exitosa
if ($result) {
    // Imprimir cada descripción seguida de un salto de línea
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['descripcion'] . "\n";
    }
} else {
    // Si hay un error, devuelve un mensaje de error.
    echo 'Error al realizar la consulta.';
}

// Cerrar la conexión
mysqli_close($con);
?>
