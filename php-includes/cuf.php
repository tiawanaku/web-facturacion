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
    public $fechaEmision;

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

    // Función personalizada para convertir números grandes a hexadecimal
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
        date_default_timezone_set('UTC');
        $dateFormat = 'YmdHisu';
        $fechaHora = $this->fechaEmision->format($dateFormat);
        $nitEmisor = str_pad($this->nitEmisor, 13, '0', STR_PAD_LEFT);
        $sucursal = str_pad($this->sucursal, 4, '0', STR_PAD_LEFT);
        $modalidad = $this->modalidad;
        $tipoEmision = $this->tipoEmision;
        $tipoFactura = $this->tipoFactura;
        $tipoDocumentoSector = str_pad($this->tipoDocumentoSector, 2, '0', STR_PAD_LEFT);
        $numeroFactura = str_pad($this->numeroFactura, 10, '0', STR_PAD_LEFT);
        $pos = str_pad($this->pos, 4, '0', STR_PAD_LEFT);

        $cadena = $nitEmisor . $fechaHora . $sucursal . $modalidad . $tipoEmision . $tipoFactura . $tipoDocumentoSector . $numeroFactura . $pos;

        echo "Concatenación de campos: " . $cadena . "\n";

        $verificador = $this->buildDigito11($cadena, 1, 9, false);
        $cadenaConModulo = $cadena . $verificador;

        echo "Resultado del módulo 11: " . $verificador . "\n";
        echo "Concatenación con módulo 11: " . $cadenaConModulo . "\n";

        $bigInt = $cadenaConModulo;
        $toHex = strtoupper($this->bigIntToHex($bigInt));

        echo "Resultado de Base 16: " . $toHex . "\n";

        // Aquí debes concatenar el código de control obtenido en el Servicio Web de solicitudCufd
        $codigoControl = "69931B9E5948E74";
        $cuf = $toHex . $codigoControl;

        return $cuf;
    }
}

// Ejemplo de uso
$cufBuilder = new SiatCufBuilder();
$cufBuilder->sucursal = "0000";
$cufBuilder->modalidad = "1";
$cufBuilder->tipoEmision = "1";
$cufBuilder->tipoFactura = "1";
$cufBuilder->nitEmisor = "0000123456789";
$cufBuilder->tipoDocumentoSector = "01";
$cufBuilder->numeroFactura = "0000000001";
$cufBuilder->pos = "0000";

// Configurar la variable fechaEmision con el valor "20190113163721231"
$cufBuilder->fechaEmision = DateTime::createFromFormat('YmdHisu', '20190113163721231');

$cuf = $cufBuilder->build();
echo "CUF: " . $cuf . "\n";




