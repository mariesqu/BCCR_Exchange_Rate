<?php
/**
 * BCCR_Exchange_Rate - PHP class to get the exchange rate from BCCR.
 *
 * @author Mario Esquivel (mariesqu/BCCR_Exchange_Rate) <mesquivel007@gmail.com>
 * @lastupdated 28/09/2020
 */

final class BCCR_Exchange_Rate
{
    // Constantes de tipo de cambio
    const COMPRA = 317;
    const VENTA = 318;
    const EURO = 333;
    const NOMBRE = "TuNombre";
    const CORREO = "TuCorreo";
    const TOKEN = "BCCR_token";

    // URL del WebService
    const IND_ECONOM_WS = "https://gee.bccr.fi.cr/Indicadores/Suscripciones/WS/wsindicadoreseconomicos.asmx";

    // Metodo que se va a utilizar del WebService
    const IND_ECONOM_METH = "ObtenerIndicadoresEconomicosXML";

    /**
     * Obtiene el tipo de cambio del dia
     *
     * @param string $tipo Tipo de cambio deseado (COMPRA/VENTA/EURO)
     * @param string $fecha Fecha del tipo de cambio deseado
     * @return float Valor del tipo de cambio
     */
    public static function obtenerTipoCambio($tipo = "", $fecha = "")
    {
        date_default_timezone_set('America/Costa_Rica');
        $fecha_tc = empty($fecha) ? date("d/m/Y") : $fecha;
        $tipo_tc = empty($tipo) ? self::COMPRA : $tipo;

        $urlWS = self::IND_ECONOM_WS . "/" . self::IND_ECONOM_METH . "?Indicador=" . $tipo_tc . "&FechaInicio=" . $fecha_tc . "&FechaFinal=" . $fecha_tc . "&Nombre=" . self::NOMBRE . "&SubNiveles=N&CorreoElectronico=" . self::CORREO . "&Token=" . self::TOKEN;
        $tipoCambio = 0;

        if (self::url_get_contents($urlWS) != false) {
            $indWS = self::url_get_contents($urlWS);
            $xml = simplexml_load_string($indWS);
            $tipo_cambio = trim(strip_tags(substr($xml, strpos($xml, "<NUM_VALOR>"), strripos($xml, "</NUM_VALOR>"))));
            $tipoCambio = number_format($tipo_cambio, 2);
        }

        return (float)$tipoCambio;
    }

    /**
     * Obtiene datos por CURL
     * @param string $Url Url del webservice
     * @return $output Respuesta del webservice
     */
    public static function url_get_contents($Url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $Url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
