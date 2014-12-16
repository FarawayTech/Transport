<?php

namespace Transport\Entity;

use Transport\Entity\Location\Station;
use Language\Normalizer;

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

    static public function createFromMongoRow($result, $lon, $lat, $query) {
        $station = new Station($result['stop_id']);
        # select proper name based on the query
        if (count($result['synonyms']) == 1) {
            $station->name = $result['synonyms'][0];
        }
        else {
            foreach ($result['synonyms'] as $name) {
                if (strpos(Normalizer::normalizeString($name), $query) !== false) {
                    $station->name = $name;
                    break;
                }
            }
        }
        $station->coordinate->x = $result['location']['coordinates'][1];
        $station->coordinate->y = $result['location']['coordinates'][0];
        $station->coordinate->type = 'WGS84';
        $station->distance = $station->coordinate->getDistanceTo($lon, $lat);
        return $station;
    }

    static public function createFromMongoCursor(\MongoCursor $cursor, $lon, $lat, $query) {
        $stations = array();
        foreach ($cursor as $result) {
            $stations[] = self::createFromMongoRow($result, $lon, $lat, $query);
        }
        return $stations;
    }
}
