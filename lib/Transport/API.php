<?php

namespace Transport;

use Buzz\Browser;
use Transport\Entity\Location\Station;
use Transport\Entity\Schedule\Route;
use Transport\Entity\Schedule\StationBoardJourney;
use Transport\Providers\Provider;
use Transport\Entity\Query;
use Transport\Entity\Location\LocationQuery;
use Transport\Entity\Location\NearbyQuery;
use Transport\Entity\Schedule\ConnectionQuery;
use Transport\Entity\Schedule\ConnectionPageQuery;
use Transport\Entity\Schedule\RouteQuery;
use Transport\Entity\Schedule\StationBoardQuery;

class API
{
    /**
     * @var Buzz\Browser
     */
    protected $browser;

    /**
     * @var Providers\Provider
     */
    protected $provider;

    /**
     * @var string
     */
    protected $lang;

    public function __construct(Provider $provider, Browser $browser = null, $lang = 'EN')
    {
        $this->browser = $browser ?: new Browser();
        $this->lang = $lang;
        $this->provider = $provider;
    }

    /**
     * @return Buzz\Message\Response
     */
    public function sendQuery(Query $query)
    {
        $headers = array();
        $headers[] = 'User-Agent: SBBMobile/4.8 CFNetwork/609.1.4 Darwin/13.0.0';
        $headers[] = 'Accept: application/xml';
        $headers[] = 'Content-Type: application/xml';

        $query->addProvider($this->provider);

        $i = 5;
        $statusCode = 0;
        $response = null;
        // try 5 times
        while ($i > 0 and $statusCode != 200) {
            $response = $this->browser->post($query->getQueryURL(), $headers, $query->toXml());
            $statusCode = $response->getStatusCode();
            $i--;
        }

        if ($statusCode == 200)
        {
            error_log($response->getContent(), 3, "sbb_api_errors.txt");
        }
        return $response;
    }

    /**
     * @param ConnectionQuery $query
     * @return array
     */
    public function findConnections(ConnectionQuery $query)
    {
        // send request
        $response = $this->sendQuery($query);

        // parse result
        $result = simplexml_load_string($response->getContent());

        // load pages
        for ($i = 0; $i < abs($query->page); $i++) {

            // load next page
            $pageQuery = new ConnectionPageQuery($query, (string) $result->ConRes->ConResCtxt);

            $response = $this->sendQuery($pageQuery);

            $result = simplexml_load_string($response->getContent());
        }

        $connections = array();
        if ($result->ConRes->ConnectionList->Connection) {
            foreach ($result->ConRes->ConnectionList->Connection as $connection) {
                $connections[] = Entity\Schedule\Connection::createFromXml($connection, $this->provider, null);
            }
        }

        return $connections;
    }

    /**
     * @param LocationQuery $query
     * @return array
     */
    public function findLocations(LocationQuery $query)
    {
        // send request
        $response = $this->sendQuery($query);

        // parse result
        $result = simplexml_load_string($response->getContent());

        $locations = array();
        $viaCount = 0;
        foreach ($result->LocValRes as $part) {

            $id = (string) $part['id'];

            // A "via" can occur 0-5 times
            if ($id == "via") {
                $id = $id.(++$viaCount);
            }

            $locations[$id] = array();
            foreach ($part->children() as $location) {

                $location = Entity\LocationFactory::createFromXml($location);
                if ($location) {
                    $locations[$id][] = $location;
                }
            }
        }

        if (count($locations) > 1) {
            return $locations;
        }
        return reset($locations);
    }

    /**
     * @param NearbyQuery $query
     * @return array
     */
    public function findNearbyLocations(NearbyQuery $query)
    {
        $url = $this->provider->URL_QUERY . '?' . http_build_query($query->toArray());

        // send request
        $response = $this->browser->get($url);

        // fix broken JSON
        $content = $response->getContent();
        // check if we need to decode
        $charset = $response->getHeaderAttribute('content-type', 'charset');
        if ($charset == 'ISO-8859-1')
            $content = utf8_encode($content);

        $content = preg_replace('/(\w+) ?:/i', '"\1":', $content);
        $content = str_replace("\\'", "'", $content);

        // parse result
        $result = json_decode($content);
        // if null, check http://www.php.net/manual/en/function.json-last-error.php

        $locations = array();
        foreach ($result->stops as $stop) {

            $location = Entity\LocationFactory::createFromJson($stop);
            if ($location) {
                $location->distance = $location->coordinate->getDistanceTo($query->lat, $query->lon);
                $locations[] = $location;
            }
        }

        return $locations;
    }

    /**
     * @param StationBoardQuery $query
     * @param Station $station
     * @return array $journeys
     */
    public function getStationBoard(StationBoardQuery $query, Station $station)
    {
        $provider = $this->provider;
        $query->addProvider($provider);
        // send request
        if ($query->isExtXML())
        {
            $response = $this->sendQuery($query);
            $result = simplexml_load_string($response->getContent());
            $journeys = StationBoardJourney::createListFromXml($result, $query->date, $provider);
        }
        else {
            $url = $query->getQueryURL() . '?' . http_build_query($query->toArray());
            // send request
            $response = $this->browser->get($url);
            $result = simplexml_load_string($provider::cleanStbXML($response->getContent()));
            $journeys = StationBoardJourney::createListFromStbXml($result, $station, $provider);
        }

        return $journeys;
    }


    public function getRoute(RouteQuery $query)
    {
        $provider = $this->provider;
        $query->addProvider($provider);
        if ($query->isExtXML())
        {
            $response = $this->sendQuery($query);
            $content = $response->getContent();
        }
        else {
            $url = $query->getQueryURL() . '?' . http_build_query($query->toArray());
            // send request
            $response = $this->browser->get($url);
            $content = $provider::cleanRouteXML($response->getContent());
        }

        // parse result
        $result = simplexml_load_string($content);

        $route = Route::createFromXml($result, $query->date, $query->station);
        return $route;
    }
}
