<?php

namespace Transport\Entity;

use Transport\Entity\Location\Station;

class LocationFactory
{
    static public function createFromXml(\SimpleXMLElement $xml)
    {
        switch ($xml->getName()) {
        case 'Poi':
            return Location\Poi::createFromXml($xml);
        case 'Station':
            return Location\Station::createFromXml($xml);
        case 'Address':
            return Location\Address::createFromXml($xml);
        case 'ReqLoc':
        case 'Err':
        default:
            return null;
        }
    }

    static public function createFromJson($json)
    {
        return Location\Station::createFromJson($json);
    }

    static public function createFromMongoCursor(\MongoCursor $cursor, $lon, $lat) {
        $stations = array();
        foreach ($cursor as $result) {
            $station = new Station($result['stop_id']);
            $station->name = $result['stop_name'];
            $station->coordinate->x = $result['stop_lat'];
            $station->coordinate->y = $result['stop_lon'];
            $station->coordinate->type = 'WGS84';
            $station->distance = $station->coordinate->getDistanceTo($lon, $lat);
            $stations[] = $station;
        }
        return $stations;
    }
}
