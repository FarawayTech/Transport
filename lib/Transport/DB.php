<?php

namespace Transport;


use Language\Normalizer;
use MongoClient;
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

    public static function findNearbyLocations($lon, $lat, $limit, $config) {
        $result = self::getCollection($config, COLLECTION)->find(Array('location' => Array('$nearSphere' => Array('$geometry' =>
            Array('type'=>'Point', 'coordinates' => Array(floatval($lon), floatval($lat)),
                // 10km max distance
                '$maxDistance'=>10000)))))->limit(intval($limit));
        $stations = LocationFactory::createFromMongoCursor($result, $lon, $lat);
        return $stations;
    }

    private static function getStataionIDs($stations) {
        $added_stations = array();
        foreach ($stations as $station) {
            $added_stations[] = $station->id;
        }
        return $added_stations;
    }

    public static function findNearbyLocationsQuery($query, $lon, $lat, $limit, $config) {
        $query = Normalizer::normalizeString($query);
        $collection = self::getCollection($config, COLLECTION);
        $stations = array();
        if ($lat && $lon) {
            // 1.1 Find local stop if exists, first on second names
            $cursor = $collection->find(Array('location' => Array('$nearSphere' =>
                Array('$geometry' => Array('type'=>'Point','coordinates' => Array(floatval($lon), floatval($lat))),
                      '$maxDistance' => 10000)), //10km max
                'second_names'=>$query))->limit(1);

            $stations = LocationFactory::createFromMongoCursor($cursor, $lon, $lat, $query);
            $limit -= count($stations);

            // 1.2 Then on first names if not found
            if (count($stations) == 0) {
                $cursor = $collection->find(Array('location' => Array('$nearSphere' =>
                    Array('$geometry' => Array('type'=>'Point','coordinates' => Array(floatval($lon), floatval($lat))),
                        '$maxDistance' => 10000)), //10km max
                    'first_names'=>$query))->limit(1);

                $stations = LocationFactory::createFromMongoCursor($cursor, $lon, $lat, $query);
                $limit -= count($stations);
            }
        }

        // 2. Weighted stations
        $cursor = $collection->find(Array('first_names' => $query, 'stop_id' => Array('$nin' => self::getStataionIDs($stations)),
            'weight' => Array('$gt' => 0)))->sort(array('weight' => -1))->limit($limit);
        $limit -= $cursor->count();
        $stations = array_merge($stations, LocationFactory::createFromMongoCursor($cursor, $lon, $lat, $query));
        if ($limit <= 0)
            return $stations;

        // 3. Everything else, TODO: preferably by ordered by distance
        // 3.1 first query on first names
        $cursor = $collection->find(Array('first_names' => $query,
            'stop_id' => Array('$nin' => self::getStataionIDs($stations))))->limit($limit);
        $limit -= $cursor->count();
        $stations = array_merge($stations, LocationFactory::createFromMongoCursor($cursor, $lon, $lat, $query));
        if ($limit <= 0)
            return $stations;

        // 3.2 then on second names
        $cursor = $collection->find(Array('second_names' => $query,
            'stop_id' => Array('$nin' => self::getStataionIDs($stations))))->limit($limit);
        $stations = array_merge($stations, LocationFactory::createFromMongoCursor($cursor, $lon, $lat, $query));

        // 4. TODO: search stations in a single-word index (to handle skips)

        return $stations;
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