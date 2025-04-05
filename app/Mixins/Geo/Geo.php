<?php


class Geo
{
    static public function get_geo_text($array, $type)
    {
        $geoWKT = "";
        switch ($type) {
            case "MULTIPOLYGON":
                $tmpPolygons = array();
                foreach ($array as $polygonsWKA) {
                    $tmpPoint = array();
                    foreach ($polygonsWKA as $pointWKA)
                        $tmpPoint[] = implode(" ", $pointWKA);
                    $tmpPolygons[] = "((" . implode(",", $tmpPoint) . "))";
                }
                $geoWKT = "MULTIPOLYGON(" . implode(",", $tmpPolygons) . ")";
                break;
            case "MULTIPOINT":
                $tmpPoint = array();
                foreach ($array as $pointWKA)
                    $tmpPoint[] = implode(" ", $pointWKA);
                $geoWKT = "MULTIPOINT(" . implode(",", $tmpPoint) . ")";
                break;
            case "POINT":
            case "point":
                $geoWKT = "POINT(" . implode(" ", $array) . ")";
                break;
            case "POLYGON":
                $polyPoints = array();
                foreach ($array as $tmpPolyPoint)
                    $polyPoints[] .= implode(" ", $tmpPolyPoint);
                $geoWKT = "POLYGON((" . implode(",", $polyPoints) . "))";
                break;
        }
        return $geoWKT;
    }

    static public function get_geo_array($text)
    {
        $geoWKA = array();
        if (strstr($text, "MULTIPOLYGON((")) {
            $geoWKA = array();
            $geoText = trim(trim($text, "MULTIPOLYGON((("), ")))");
            if (!empty($geoText)) {
                $geoText = "($geoText)";
                $tmpGeoWKTs = explode("),(", $geoText);
                foreach ($tmpGeoWKTs as $tmpPolygonWKT) {
                    $tmpGeoText = trim(trim($tmpPolygonWKT, "("), ")");
                    $tmpGeoWKAs = explode(",", $tmpGeoText);
                    $polygonWKA = array();
                    foreach ($tmpGeoWKAs as $tmpPolyPoint)
                        $polygonWKA[] = explode(" ", trim($tmpPolyPoint));
                    $geoWKA[] = $polygonWKA;
                }
            }
        } elseif (strstr($text, "MULTIPOINT(")) {
            $geoWKA = array();
            $geoText = trim(trim($text, "MULTIPOINT("), ")");
            if (!empty($geoText)) {
                $tmpGeoWKAs = explode(",", $geoText);
                foreach ($tmpGeoWKAs as $tmpGeoWKA)
                    $geoWKA[] = explode(" ", trim($tmpGeoWKA));
            }
        } elseif (strstr($text, "POINT(")) {
            $geoText = trim(trim($text, "POINT("), ")");
            if (!empty($geoText))
                $geoWKA = explode(" ", $geoText);
        } elseif (strstr($text, "point(")) {
            $geoText = trim(trim($text, "point("), ")");
            if (!empty($geoText))
                $geoWKA = explode(",", $geoText);
        } elseif (strstr($text, "POLYGON((")) {
            $geoText = trim(trim($text, "POLYGON(("), "))");
            if (!empty($geoText)) {
                $tmpPolyPoints = explode(",", $geoText);
                foreach ($tmpPolyPoints as $tmpPolyPoint)
                    $geoWKA[] = explode(" ", $tmpPolyPoint);
            }
        }
        return $geoWKA;
    }

    static function getST_AsTextFromBinary($binary)
    {
        // like => POINT(36.36822190085111 59.52341079711915)

        $coordinates = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $binary);

        try {
            $point = 'POINT(';
            $point .= $coordinates['lat'];
            $point .= ' ' . $coordinates['lon'];
            $point .= ')';

            return $point;
        } catch (Exception $e) {

        }
    }
}
