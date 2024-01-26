<?php

// Función para obtener la fecha y hora actuales en el formato especificado
function getFormattedDateTime() {
    // Formato: 2024-01-25T20:45:13.218
    // Nota: PHP no maneja milisegundos, así que se agrega '.000' manualmente
    return date('Y-m-d\TH:i:s') . '.000';
}

// Contenido XML con un marcador de posición para la fecha de emisión
$xmlContent = '<?xml version="1.0" encoding="UTF-8"?> <facturaComputarizadaCompraVenta xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaComputarizadaCompraVenta.xsd"> <cabecera> <nitEmisor>343322026</nitEmisor>
<razonSocialEmisor>CEGEPA SRL</razonSocialEmisor>
<municipio>La Paz</municipio>
<telefono>76543213</telefono>
<numeroFactura>1</numeroFactura>
<cuf>5BC30BD9E04E02AAB35CB510C89EE381FD02558606A469931B9E5948E74</cuf>
<cufd>BQUE5Q1doaUFBNzTgwMzg1NENFMDY=Q8KhMGluUWFCWVVc2RTU4NzA3M0VBM</cufd>
<codigoSucursal>0</codigoSucursal>
<direccion>AV. JORGE LOPEZ #123</direccion>
<codigoPuntoVenta xsi:nil="true" />
<fechaEmision>FECHA_AQUI</fechaEmision>
<nombreRazonSocial>Hilaquita</nombreRazonSocial>
<codigoTipoDocumentoIdentidad>1</codigoTipoDocumentoIdentidad>
<numeroDocumento>12831711016</numeroDocumento>
<complemento xsi:nil="true" />
<codigoCliente>12831711016</codigoCliente>
<codigoMetodoPago>1</codigoMetodoPago>
<numeroTarjeta xsi:nil="true" />
<montoTotal>99</montoTotal>
<montoTotalSujetoIva>99</montoTotalSujetoIva>
<codigoMoneda>1</codigoMoneda>
<tipoCambio>1</tipoCambio>
<montoTotalMoneda>99</montoTotalMoneda>
<montoGiftCard xsi:nil="true" />
<descuentoAdicional>1</descuentoAdicional>
<codigoExcepcion xsi:nil="true" />
<cafc xsi:nil="true" />
<leyenda>Ley N° 453: Puedes acceder a la reclamación cuando tus derechos han sido vulnerados.</leyenda>
<usuario>pperez</usuario>
<codigoDocumentoSector>1</codigoDocumentoSector>
</cabecera>
<detalle>
<actividadEconomica>001220</actividadEconomica>
<codigoProductoSin>99100</codigoProductoSin>
<codigoProducto>JN-131231</codigoProducto>
<descripcion>JUGO DE NARANJA EN VASO</descripcion>
<cantidad>1</cantidad>
<unidadMedida>1</unidadMedida>
<precioUnitario>100</precioUnitario>
<montoDescuento>0</montoDescuento>
<subTotal>100</subTotal>
<numeroSerie>0</numeroSerie>
<numeroImei>0</numeroImei>
</detalle>
</facturaComputarizadaCompraVenta>';

// Reemplazar el marcador de posición con la fecha y hora actuales
$xmlContent = str_replace('FECHA_AQUI', getFormattedDateTime(), $xmlContent);

// Guardar el contenido XML en un archivo temporal
$tempXml = 'temp.xml';
file_put_contents($tempXml, $xmlContent);

// Comprimir en GZIP
$gzipPath = 'archivo_comprimido.xml.gz';
$gz = gzopen($gzipPath, 'w9'); // 'w9' es el nivel de compresión máximo
gzwrite($gz, $xmlContent);
gzclose($gz);

echo "Archivo comprimido: $gzipPath\n";

// Calcular el hash SHA256
$sha256Hash = hash_file('sha256', $gzipPath);
echo "Hash SHA256: $sha256Hash\n";

// Codificar el archivo comprimido en Base64 y mostrarlo en pantalla
$base64Encoded = base64_encode(file_get_contents($gzipPath));
echo "Cadena Base64 del archivo comprimido:\n$base64Encoded\n";

?>
