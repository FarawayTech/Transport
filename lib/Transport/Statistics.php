<?php

namespace Transport;

use Predis\Client;
use Transport\Entity\Location\Location;
use Transport\Entity\Location\Station;

class Statistics {

    protected $redis;

    public function __construct(Client $redis = null)
    {
        $this->redis = $redis;
    }

    public function call()
    {
        if ($this->redis) {
            $date = date('Y-m-d');
            $prefix = "stats:calls";
            $key = "$prefix:$date";
            $this->redis->sadd($prefix, $key);
            $this->redis->incr($key);
        }
    }

    public function station(Location $station)
    {
        if ($station instanceof Station) {
            $this->count('stats:stations', $station->id, array('name' => $station->name, 'x' => $station->coordinate->x, 'y' => $station->coordinate->y));
        }
    }

    public function resource($path)
    {
        $this->count('stats:resources', $path, array('path' => $path));
    }

    protected function nameNumber($name, $number, $category, $shortCategory) {
        if ($shortCategory == 'B' || $shortCategory == 'T')
            return;
        if ($this->redis) {
            $key = "stats:namenumbers:$number";
            $this->redis->sadd($key, $name.';'.$shortCategory.';'.$category);
            $this->redis->sadd("stats:namenumbers", $key);
        }
    }

    protected function count($prefix, $id, $data)
    {
        if ($this->redis) {
            $key = "$prefix:$id";
            $this->redis->hmset($key, $data);
            $this->redis->sadd($prefix, $key);
            $this->redis->hincrby($key, 'calls', 1);
        }
    }

    public function getCalls()
    {
	    $keys = $this->redis->keys('stats:calls:*');

        $result = $this->redis->sort("stats:calls", array(
            'get' => array('#', '*'),
            'sort'  => 'ASC',
            'alpha' => true
        ));

	    // regroup
	    $calls = array();
	    foreach (array_chunk($result, 2) as $values) {
	        $calls[substr($values[0], 12, 10)] = $values[1];
	    }

	    return $calls;
    }

    public function getTopResources()
    {
        return $this->top('stats:resources', array('path', 'calls'));
    }

    public function getTopStations()
    {
        return $this->top('stats:stations', array('name', 'x', 'y', 'calls'));
    }

    public function stationboardNumbers($stationboard)
    {
        foreach ($stationboard as $journey) {
            $name = preg_replace('/\d/', '$', $journey->name);
            $number = preg_replace('/\d/', '$', $journey->resolvedNumber);
            $this->nameNumber($name, $number, $journey->category, $journey->shortCategory);
        }

    }

    public function getAllNames() {
        // regroup
        $data = array();
        foreach ($this->redis->smembers("stats:namenumbers") as $key) {
            $data[explode(":", $key)[2]] = $this->redis->smembers($key);
        }

        return $data;
    }

    protected function top($key, $fields)
    {
        $result = $this->redis->sort($key, array(
            'by' => '*->calls',
            'limit' => array(0, 5),
            'get' => array_map(function ($value) { return "*->$value"; }, $fields),
            'sort'  => 'DESC',
        ));

        // regroup
        $data = array();
        foreach (array_chunk($result, count($fields)) as $values) {
            $data[] = array_combine($fields, $values);
        }

        return $data;
    }
}
