<?php

namespace Transport\Entity;

class Coordinate
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $x;

    /**
     * @var int
     */
    public $y;

    private function populateCoordinate($x, $y)
    {
        $x = self::intToFloat((string) $x);
        $y = self::intToFloat((string) $y);
        if ($y > $x) { // HAFAS bug, returns inverted lat/long
            $this->x = $y;
            $this->y = $x;
        } else {
            $this->x = $x;
            $this->y = $y;
        }
    }

    /**
     * Factory method to create an instance of Coordinate and extract the data from the given xml
     *
     * @param   \SimpleXMLElement   $xml    The item xml
     * @return  Coordinate          The created instance
     */
    static public function createFromXml(\SimpleXMLElement $xml)
    {
        $coordinate = new Coordinate();
        $coordinate->type = (string) $xml['type'];
        $coordinate->populateCoordinate($xml['x'], $xml['y']);
        return $coordinate;
    }

    static public function createFromJson($json)
    {
        $coordinate = new Coordinate();
        $coordinate->type = 'WGS84'; // best guess
        $coordinate->populateCoordinate($json->x, $json->y);
        return $coordinate;
    }

    static public function floatToInt($float)
    {
        return sprintf('%01.6f', $float) * 1000000;
    }

    static public function intToFloat($int)
    {
        return $int / 1000000;
    }

    /**
     * Calculates the distance to another coordinate using the Haversine Formula.
     * Not really accurate.
     */
    public function getDistanceTo($lon, $lat)
    {
        $earth_radius = 6371;

        $dLon = deg2rad($this->y - $lon);
        $dLat = deg2rad($this->x - $lat);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat)) * cos(deg2rad($this->x)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $d = $earth_radius * $c;

        return round($d*1000, 1);
    }
}
