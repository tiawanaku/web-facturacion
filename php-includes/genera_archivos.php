<?php
require_once 'cuf.php';

// Función para obtener la fecha y hora actuales en el formato especificado
function getFormattedDateTime() {
    // Establecer la zona horaria a UTC
    date_default_timezone_set('UTC');
    // Formato: Y-m-d\TH:i:s (año-mes-díaT hora:minuto:segundo)
    // PHP no maneja milisegundos, así que se agrega '.000' manualmente
    return date('Y-m-d\TH:i:s') . '.000';
}
// Crear una instancia de SiatCufBuilder y configurar sus propiedades
$cufBuilder = new SiatCufBuilder();
$cufBuilder->sucursal = "0000"; // Configura estos valores según sea necesario
$cufBuilder->modalidad = "2";
$cufBuilder->tipoEmision = "1";
$cufBuilder->tipoFactura = "1";
$cufBuilder->nitEmisor = "0000343322026";
$cufBuilder->tipoDocumentoSector = "01";
$cufBuilder->numeroFactura = "0000000001";
$cufBuilder->pos = "0000";

// Generar el CUF
$cuf = $cufBuilder->build();
echo "CUF generado: " . $cuf . "\n";

// Contenido XML con un marcador de posición para el CUF y la fecha de emisión
$xmlContent = '...<cuf>AQUI</cuf>...';

// Reemplazar el marcador de posición del CUF
$xmlContent = str_replace('AQUI', $cuf, $xmlContent);

// Continuar con el resto del XML
$xmlContent .= '<?xml version="1.0" encoding="UTF-8"?> <facturaComputarizadaCompraVenta
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="facturaComputarizadaCompraVenta.xsd">
    <cabecera>
        <nitEmisor>343322026</nitEmisor>...';

        // Reemplazar el marcador de posición con la fecha y hora actuales
        $xmlContent = str_replace('FECHA_AQUI', getFormattedDateTime(), $xmlContent);

        // Contenido XML con un marcador de posición para la fecha de emisión
        $xmlContent = '
        <?xml version="1.0" encoding="UTF-8"?>
        <facturaComputarizadaCompraVenta xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="facturaComputarizadaCompraVenta.xsd">
            <cabecera>
                <nitEmisor>343322026</nitEmisor>
                <razonSocialEmisor>CEGEPA SRL</razonSocialEmisor>
                <municipio>La Paz</municipio>
                <telefono>76543213</telefono>
                <numeroFactura>1</numeroFactura>
                <cuf>AQUI</cuf>
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

        // Crear cliente SOAP con la opción 'trace' habilitada
        $clienteSoap = new SoapClient(null, [
        'location' => 'https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionCompraVenta',
        'uri' => 'https://siat.impuestos.gob.bo/',
        'trace' => 1, // Habilitar trazado
        'stream_context' => stream_context_create([
        'http' => [
        'header' => 'apikey: TokenApi
        eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJDRUdFUEEiLCJjb2RpZ29TaXN0ZW1hIjoiNzc2RTU4NzA3M0VBMTgwMzg1NENFMDYiLCJuaXQiOiJINHNJQUFBQUFBQUFBRE0yTVRZMk1qSXdNZ01BUm9BMWJBa0FBQUE9IiwiaWQiOjYyNzA3MiwiZXhwIjoxNzMxNDU2MDAwLCJpYXQiOjE2OTk5OTE3ODYsIm5pdERlbGVnYWRvIjozNDMzMjIwMjYsInN1YnNpc3RlbWEiOiJTRkUifQ.taqReXfn8lmMVPs1RUQMO_-KRnUeE9CLEN2Y55NhoCn-tjX-F8EA9fJomvimFgG3tKRz3QmPQDAXqePjZij2NQ'
        ]
        ])
        ]);

        // Construir el cuerpo del mensaje SOAP
        $soapRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:siat="https://siat.impuestos.gob.bo/">
            <soapenv:Header />
            <soapenv:Body>
                <siat:recepcionFactura>
                    <SolicitudServicioRecepcionFactura>
                        <codigoAmbiente>2</codigoAmbiente>
                        <codigoDocumentoSector>1</codigoDocumentoSector>
                        <codigoEmision>1</codigoEmision>
                        <codigoModalidad>2</codigoModalidad>
                        <!--Optional:-->
                        <codigoPuntoVenta>0</codigoPuntoVenta>
                        <codigoSistema>776E587073EA1803854CE06</codigoSistema>
                        <codigoSucursal>0</codigoSucursal>
                        <cufd>BQUE5Q1doaUFBNzTgwMzg1NENFMDY=Q8KhMGluUWFCWVVc2RTU4NzA3M0VBM</cufd>
                        <cuis>C85F79A6</cuis>
                        <nit>343322026</nit>
                        <tipoFacturaDocumento>1</tipoFacturaDocumento>
                        <archivo>' . base64_encode(file_get_contents($gzipPath)) . '</archivo>
                        <fechaEnvio>' . getFormattedDateTime() . '</fechaEnvio>
                        <hashArchivo>' . $sha256Hash . '</hashArchivo>
                    </SolicitudServicioRecepcionFactura>
                </siat:recepcionFactura>
            </soapenv:Body>
        </soapenv:Envelope>';

        // Realizar la llamada SOAP
        try {
        $clienteSoap->__doRequest($soapRequest,
        'https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionCompraVenta', '', 1);
        // Imprimir la respuesta SOAP
        echo "Respuesta SOAP:\n" . $clienteSoap->__getLastResponse();
        } catch (SoapFault $e) {
        echo "Error SOAP: " . $e->getMessage();
        }

        ?>