<?php

namespace Transport;


use Language\Normalizer;
use MongoClient;
use SplMaxHeap;
use Transport\Entity\LocationFactory;

const COLLECTION = "stops";
const SMS_COLLECTION = "sms_tickets";

class DB {

    private static function getCollection($config, $collection) {
        return self::getDB($config)->selectCollection($collection);
    }

    private static function getDB($config) {
        $m = new MongoClient($config);
        $dbname = substr(parse_url($config, PHP_URL_PATH), 1);
        return $m->selectDB($dbname);
    }

    private static function sortByDistance($array) {
        usort($array, function($a, $b) {
            return $a->distance - $b->distance;
        });
        return $array;
    }

    private static function getMinDistanceFromCursor($cursor, $lon, $lat, $limit, $query) {
        $heap = new StationDistanceHeap($limit);
        foreach ($cursor as $result) {
            $heap->insert(LocationFactory::createFromMongoRow($result, $lon, $lat, $query));
        }
        // extra $limit elements
        $result = array();
        for ($i = 0; $i < $limit && !$heap->isEmpty(); $i++) {
            $result[] = $heap->extract();
        }
        return $result;
    }

    public static function findNearbyLocations($lon, $lat, $limit, $config) {
        $result = self::getCollection($config, COLLECTION)->find(Array('location' => Array('$nearSphere' => Array('$geometry' =>
            Array('type'=>'Point', 'coordinates' => Array(floatval($lon), floatval($lat)),
                // 10km max distance
                '$maxDistance'=>10000)))))->limit(intval($limit));
        $stations = LocationFactory::createFromMongoCursor($result, $lon, $lat, '');
        return $stations;
    }

    public static function findNearbyLocationsQuery($query, $lon, $lat, $limit, $config) {
        $query = Normalizer::normalizeString($query);
        $stations = self::initialNearbyLocationsQuery($query, $lon, $lat, $limit, $config);
        $insert_index = 0;
        foreach ($stations as $station) {
            $station_processed = Normalizer::normalizeString($station->name);
            // doesn't start with a query - go on
            if (strpos($station_processed, $query) != 0) {
                $insert_index += 1;
            };
        }

        // search for weighted stations
        $weight_limit = sizeof($stations) - $insert_index;
        $extra_stations = array();
        if ($weight_limit > 0) {
            $collection = self::getCollection($config, COLLECTION);
            $extra_cursor = $collection->find(Array('prefix_names' => $query,
                'weight' => Array('$gt' => 0)))->sort(array('weight' => -1))->limit($weight_limit);
            $extra_stations = LocationFactory::createFromMongoCursor($extra_cursor, $lon, $lat, $query);
        }
        array_splice($stations, $insert_index, 0, $extra_stations);
        return array_slice($stations, 0, $limit);
    }

    private static function initialNearbyLocationsQuery($query, $lon, $lat, $limit, $config) {
        $db = self::getDB($config);
        $collection = self::getCollection($config, COLLECTION);
        $count = $db->command(array('collStats' => 'stops'))['count'];
        // find amount of full name stops
        $full_cursor = $collection->find(Array('names'=> $query));
        $prefix_cursor = $collection->find(Array('prefix_names'=> $query,
                                                 'names'=>Array('$ne'=> $query)));
        $full_count = $full_cursor->count();
        $stations = array();

        if ($full_count < $limit) {
            if ($full_count > 0) {
                $stations = self::sortByDistance(LocationFactory::createFromMongoCursor($full_cursor, $lon, $lat, $query));
                $limit = $limit - $full_count;
            }
        }
        else if ($count*$limit/$full_count < $full_count) {
            $result_cursor = $collection->find(Array('location' => Array('$nearSphere' =>
                Array('$geometry' => Array('type'=>'Point', 'coordinates' => Array(floatval($lon), floatval($lat))))),
                'names'=>$query))->limit(intval($limit));
            return LocationFactory::createFromMongoCursor($result_cursor, $lon, $lat, $query);
        }
        else {
            // sort $full_cursor by distance and get limit
            return self::getMinDistanceFromCursor($full_cursor, $lon, $lat, $limit, $query);
        }

        // full count was less than limit or 0, get prefix count
        $prefix_count = $prefix_cursor->count();
        if ($prefix_count < $limit) {
            return array_merge($stations, self::sortByDistance(LocationFactory::createFromMongoCursor($prefix_cursor, $lon, $lat, $query)));
        }
        else if ($count*$limit/$prefix_count < $prefix_count) {
            $result_cursor = $collection->find(Array('location' => Array('$nearSphere' =>
                Array('$geometry' => Array('type'=>'Point', 'coordinates' => Array(floatval($lon), floatval($lat))))),
                'prefix_names'=> $query, 'names'=>Array('$ne'=> $query)))->limit(intval($limit));
            return array_merge($stations, LocationFactory::createFromMongoCursor($result_cursor, $lon, $lat, $query));
        }
        else {
            // sort $prefix_cursor by distance and get limit, merge with $stations
            return array_merge($stations, self::getMinDistanceFromCursor($prefix_cursor, $lon, $lat, $limit, $query));
        }

    }

    public static function populateSMSTicketing($stations, $config) {
        $collection = self::getCollection($config, SMS_COLLECTION);
        $cache = array();
        foreach ($stations as $station) {
            $main_name = explode(",", $station->name)[0];
            if (array_key_exists($main_name, $cache))
                $result = $cache[$main_name];
            else
                $result = $collection->findOne(array('localities.name' => $main_name), array('localities' => 0, '_id' => 0));
            if ($result) {
                $station->sms_ticket = $result;
                $cache[$main_name] = $result;
            }
        }
    }

}

class StationDistanceHeap extends SplMaxHeap
{
    function compare($a, $b)
    {
        return  $b->distance - $a->distance;
    }
}