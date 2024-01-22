<?php
// Incluye el archivo de conexión
include('connect.php');

$descripcionProducto = "";

// Verifica si el código del producto está establecido
if(isset($_GET['codigoProducto'])) {
    $codigoProducto = $_GET['codigoProducto'];

    // Prepara la consulta SQL
    $query = mysqli_prepare($con, "SELECT descripcionProducto FROM cegepafacturacion.sincronizarlistaproductosservicios WHERE codigoProducto = ? LIMIT 1");
    mysqli_stmt_bind_param($query, "s", $codigoProducto);

    // Ejecuta la consulta
    mysqli_stmt_execute($query);

    // Almacena el resultado
    $resultado = mysqli_stmt_get_result($query);

    // Obtiene la fila del resultado
    $fila = mysqli_fetch_assoc($resultado);

    // Guarda la descripción del producto
    if ($fila) {
        $descripcionProducto = $fila['descripcionProducto'];
    }

    // Cierra la consulta
    mysqli_stmt_close($query);
}

// Cierra la conexión a la base de datos
mysqli_close($con);
?>

