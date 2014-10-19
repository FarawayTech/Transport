<?php 

namespace Transport\Entity\Location;

use Buzz\Browser;
use Buzz\Message\Response;
use MongoClient;
use Transport\Entity\Coordinate;
use Transport\ISendQuery;
use Transport\Providers\Provider;

class NearbyQuery implements ISendQuery
{
    public $lat;
    public $lon;
    public $limit;
    protected $provider;

    public function __construct($lat, $lon, $limit = null)
    {
        if (is_null($limit))
            $limit = 10;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->limit = $limit;
    }

    public function addProvider(Provider $provider) {
        $this->provider = $provider;
    }

    public function toArray()
    {
        return array(
            'performLocating' => '2',
            'tpl' => 'stop2json',
            'look_maxno' => $this->limit,
            'look_stopclass' => 1023, // all, 1<<10 - 1
            'look_maxdist' => 5000,
            'look_y' => Coordinate::floatToInt($this->lat),
            'look_x' => Coordinate::floatToInt($this->lon),
        );
    }

    /**
     * Default implementation
     * @param \Buzz\Browser $browser
     * @return Response
     */
    public function __sendQuery(Browser $browser)
    {
        if ($this->provider->isNearByLocal() && $this->provider->MONGO_URL)
        {
            // send request to mongo
            $m = new MongoClient($this->provider->MONGO_URL);
            $collection = $m->selectDB("cc_DYGdzXUnNrOg")->selectCollection("ch_stops");
            $result = $collection->find(Array('loc' => Array('$nearSphere' => Array('$geometry' =>
            Array('type'=>'Point', 'coordinates' => Array($this->lon, $this->lat))))))->limit($this->limit);
            var_dump($result);
        }
        else {
            $url = $this->provider->URL_QUERY . '?' . http_build_query($this->toArray());
            // send request
            return $browser->get($url);
        }
    }
}
