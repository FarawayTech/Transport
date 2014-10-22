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
            $station->name = $result['canonical_name'];
            $station->coordinate->x = $result['location']['coordinates'][1];
            $station->coordinate->y = $result['location']['coordinates'][0];
            $station->coordinate->type = 'WGS84';
            $station->distance = $station->coordinate->getDistanceTo($lon, $lat);
            $stations[] = $station;
        }
        return $stations;
    }
}
