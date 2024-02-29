<?php

class SiatCufBuilder {
    public $sucursal;
    public $modalidad;
    public $tipoEmision;
    public $tipoFactura;
    public $nitEmisor;
    public $tipoDocumentoSector;
    public $numeroFactura;
    public $pos;

    public function buildDigito11($cadena, $numDig, $limMult, $x10) {
        if (!$x10) {
            $numDig = 1;
        }

        for ($n = 1; $n <= $numDig; $n++) {
            $dig = 0;
            $suma = 0;
            $mult = 2;

            for ($i = strlen($cadena) - 1; $i >= 0; $i--) {
                $suma += $mult * intval(substr($cadena, $i, 1));
                if (++$mult > $limMult) {
                    $mult = 2;
                }
            }

            if ($x10) {
                $dig = ($suma * 10) % 11 % 10;
            } else {
                $dig = $suma % 11;
            }

            if ($dig == 10) {
                $cadena .= "1";
            }

            if ($dig == 11) {
                $cadena .= "0";
            }

            if ($dig < 10) {
                $cadena .= strval($dig);
            }
        }

        return substr($cadena, -1 * $numDig);
    }

    public function bigIntToHex($bigInt) {
        $hex = '';
        while (bccomp($bigInt, '0') > 0) {
            $remainder = bcmod($bigInt, '16');
            $hex = dechex(intval($remainder)) . $hex;
            $bigInt = bcdiv($bigInt, '16', 0);
        }
        return strtoupper($hex);
    }

    public function build() {
        date_default_timezone_set('America/La_Paz');
        $dateTime = new DateTime();
        $fechaHora = $dateTime->format('YmdHis') . substr(sprintf('%06d', $dateTime->format('u')), 0, 3);

        $nitEmisor = str_pad($this->nitEmisor, 13, '0', STR_PAD_LEFT);
        $sucursal = strlen($this->sucursal) > 4 ? substr($this->sucursal, 0, 4) : $this->sucursal;
        $modalidad = $this->modalidad;
        $tipoEmision = $this->tipoEmision;
        $tipoFactura = $this->tipoFactura;
        $tipoDocumentoSector = strlen($this->tipoDocumentoSector) > 2 ? substr($this->tipoDocumentoSector, 0, 2) : $this->tipoDocumentoSector;
        $numeroFactura = strlen($this->numeroFactura) > 10 ? substr($this->numeroFactura, 0, 10) : $this->numeroFactura;
        $pos = str_pad($this->pos, 4, '0', STR_PAD_LEFT);

        $cadena = $nitEmisor . $fechaHora . $sucursal . $modalidad . $tipoEmision . $tipoFactura . $tipoDocumentoSector . $numeroFactura . $pos;

        $verificador = $this->buildDigito11($cadena, 1, 9, false);
        $cadenaConModulo = $cadena . $verificador;

        $bigInt = $cadenaConModulo;
        $toHex = strtoupper($this->bigIntToHex($bigInt));

        $codigoControl = "596EAEB19168E74"; // Este valor debe ser reemplazado por el código de control real
        $cuf = $toHex . $codigoControl;

        return $cuf;

    }
}

function getFormattedDateTime() {
    date_default_timezone_set('America/La_Paz');
    $dateTime = new DateTime();
    // Obteniendo los primeros tres dígitos de los microsegundos
    $fechaHora = $dateTime->format('Y-m-d\TH:i:s') . '.' . substr(sprintf('%06d', $dateTime->format('u')), 0, 3);
    return $fechaHora;
}

// Ejemplo de uso
$cufBuilder = new SiatCufBuilder();
$cufBuilder->sucursal = "0000";
$cufBuilder->modalidad = "2";
$cufBuilder->tipoEmision = "1";
$cufBuilder->tipoFactura = "1";
$cufBuilder->nitEmisor = "0000343322026";
$cufBuilder->tipoDocumentoSector = "01";
$cufBuilder->numeroFactura = "0000000001";
$cufBuilder->pos = "0000";

$cuf = $cufBuilder->build();
echo "CUF: " . $cuf . "\n";
// Contenido XML con un marcador de posición para el CUF y la fecha de emisión
$xmlContent = '
<facturaComputarizadaCompraVenta xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="facturaComputarizadaCompraVenta.xsd">
    <cabecera>
        <nitEmisor>343322026</nitEmisor>
        <razonSocialEmisor>CEGEPA SRL</razonSocialEmisor>
        <municipio>La Paz</municipio>
        <telefono>76543213</telefono>
        <numeroFactura>1</numeroFactura>
        <cuf>' . $cuf . '</cuf>
        <cufd>BQTlDV2hpQUE=NzTgwMzg1NENFMDY=Q3xNVkxLQkRZVUFc2RTU4NzA3M0VBM</cufd>
        <codigoSucursal>0</codigoSucursal>
        <direccion>AV. JORGE LOPEZ #123</direccion>
        <codigoPuntoVenta xsi:nil="true" />
        <fechaEmision>' . getFormattedDateTime() . '</fechaEmision>
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

// Guardar el contenido XML en un archivo temporal
$tempXml = 'temp.xml';
file_put_contents($tempXml, $xmlContent);

// Comprueba si se ha guardado correctamente
if (file_exists($tempXml)) {
    echo "Contenido de XML guardado en $tempXml:\n" . file_get_contents($tempXml) . "\n";
} else {
    echo "Error al guardar el archivo XML.\n";
}

// Comprimir en GZIP
$gzipPath = 'archivo_comprimido.xml.gz';
$gz = gzopen($gzipPath, 'w9'); // 'w9' es el nivel de compresión máximo
gzwrite($gz, file_get_contents($tempXml));
gzclose($gz);

echo "Archivo comprimido: $gzipPath\n";

// Calcular el hash SHA256
$sha256Hash = hash_file('sha256', $gzipPath);
echo "Hash SHA256: $sha256Hash\n";

// Codificar el archivo comprimido en Base64 y mostrarlo en pantalla
$base64Encoded = base64_encode(file_get_contents($gzipPath));
echo "Cadena Base64 del archivo comprimido:\n$base64Encoded\n";

$soapRequest = '<siat:recepcionFactura xmlns:siat="https://siat.impuestos.gob.bo/">
            <SolicitudServicioRecepcionFactura>
                <codigoAmbiente>2</codigoAmbiente>
                <codigoDocumentoSector>1</codigoDocumentoSector>
                <codigoEmision>1</codigoEmision>
                <codigoModalidad>2</codigoModalidad>
                <!--Optional:-->
                <codigoPuntoVenta>0</codigoPuntoVenta>
                <codigoSistema>776E587073EA1803854CE06</codigoSistema>
                <codigoSucursal>0</codigoSucursal>
                <cufd>BQTlDV2hpQUE=NzTgwMzg1NENFMDY=Q3xNVkxLQkRZVUFc2RTU4NzA3M0VBM</cufd>
                <cuis>C85F79A6</cuis>
                <nit>343322026</nit>
                <tipoFacturaDocumento>1</tipoFacturaDocumento>
                <archivo>' . $base64Encoded . '</archivo>
                <fechaEnvio>' . getFormattedDateTime() . '</fechaEnvio>
                <hashArchivo>' . $sha256Hash . '</hashArchivo>
            </SolicitudServicioRecepcionFactura>
        </siat:recepcionFactura>';

// Inicializar cURL
$ch = curl_init();

// Configurar opciones de cURL
curl_setopt($ch, CURLOPT_URL, "http://localhost:5011/emision");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $soapRequest);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/plain; charset=utf-8"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Enviar la solicitud
$response = curl_exec($ch);

// Verificar si hubo un error
if (curl_errno($ch)) {
    echo 'Error en la solicitud cURL: ' . curl_error($ch);
} else {
    // Procesar la respuesta
    echo "Respuesta recibida:\n" . $response;
}

// Cerrar la sesión cURL
curl_close($ch);
echo 'Valor de xml: ' . htmlspecialchars($xmlContent);
echo 'Valor de tiempo: ' . getFormattedDateTime();
?>