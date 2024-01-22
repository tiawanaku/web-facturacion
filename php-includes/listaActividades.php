<?php
// Incluir el archivo de conexión solo una vez
include '../php-includes/connect.php';

// Determinar el código de actividad basado en la descripción seleccionada
$codigoActividad = isset($_GET['codigoActividad']) && $_GET['codigoActividad'] == 'Actividad Secundaria' ? '001220' : '464300';

// Primera consulta SQL
$sql1 = "SELECT
            CASE
                WHEN tipo_actividad = 'P' THEN 'Actividad Principal'
                WHEN tipo_actividad = 'S' THEN 'Actividad Secundaria'
                ELSE 'Otro'
            END AS actividad_descripcion
        FROM cegepafacturacion.sincronizaractiviades";

// Ejecutar la primera consulta
$result1 = mysqli_query($con, $sql1);

// Preparar el arreglo para las actividades
$actividades = [];

// Verificar si la consulta fue exitosa
if($result1){
    while($row = mysqli_fetch_assoc($result1)){
        $actividades[] = $row['actividad_descripcion'];
    }
} else {
    $actividades['error'] = 'Error al realizar la consulta de actividades.';
}

// Segunda consulta SQL
$sql2 = "SELECT codigoProducto FROM cegepafacturacion.sincronizarlistaproductosservicios
where codigoActividad = '$codigoActividad'";

// Ejecutar la segunda consulta
$result2 = mysqli_query($con, $sql2);

// Preparar el arreglo para los códigos de producto
$codigosProductos = [];

// Verificar si la consulta fue exitosa
if($result2){
    while($row = mysqli_fetch_assoc($result2)){
        $codigosProductos[] = $row['codigoProducto'];
    }
} else {
    $codigosProductos['error'] = 'Error al realizar la consulta de productos.';
}

// Combinar los resultados en un solo arreglo
$resultados = array(
    'actividades' => $actividades,
    'codigosProductos' => $codigosProductos
);

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($resultados);

// Cerrar la conexión
mysqli_close($con);
?>
