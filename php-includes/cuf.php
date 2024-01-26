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

        $codigoControl = "07511A789948E74"; // Este valor debe ser reemplazado por el código de control real
        $cuf = $toHex . $codigoControl;

        return $cuf;
    }
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