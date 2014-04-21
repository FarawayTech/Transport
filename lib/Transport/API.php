<?php

namespace Transport;

use Buzz\Browser;
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

        return $this->browser->post($query->getQueryURL(), $headers, $query->toXml());
    }

    /**
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
                $connections[] = Entity\Schedule\Connection::createFromXml($connection, null);
            }
        }

        return $connections;
    }

    /**
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
     * @return array
     */
    public function findNearbyLocations(NearbyQuery $query)
    {
        $url = $this->provider->URL_QUERY . '?' . http_build_query($query->toArray());

        // send request
        $response = $this->browser->get($url);

        // fix broken JSON
        $content = $response->getContent();
        $content = preg_replace('/(\w+) ?:/i', '"\1":', $content);
        $content = str_replace("\\'", "'", $content);

        // parse result
        $result = json_decode($content);

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
     * @param Entity\Station $station
     * @param string $boardType
     * @param int $maxJourneys
     * @param string $dateTime
     * @param array $transportationTypes
     */
    public function getStationBoard(StationBoardQuery $query)
    {
        // send request
        $response = $this->sendQuery($query);

        // parse result
        $result = simplexml_load_string($response->getContent());

        // since the stationboard always lists all connections starting from now we just use the date
        // and wrap it accordingly if time goes over midnight
        $journeys = array();
        // subtract one minute because SBB also returns results for one minute in the past
        $prevTime = time() - 60;
        $date = $query->date;
        if ($result->STBRes->JourneyList->STBJourney) {
            foreach ($result->STBRes->JourneyList->STBJourney as $journey) {
                $curTime = strtotime((string) $journey->MainStop->BasicStop->Dep->Time);
                $prognosis = strtotime((string) $journey->MainStop->BasicStop->StopPrognosis->Dep->Time);
                if (!$prognosis)
                    $prognosis = $curTime;
                if ($prevTime > $curTime && $prevTime > $prognosis) { // we passed midnight
                    $date->add(new \DateInterval('P1D'));
                }
                $journeys[] = Entity\Schedule\StationBoardJourney::createFromXml($journey, $date, null);
                $prevTime = $curTime;
            }
        }

        return $journeys;
    }


    public function getRoute(RouteQuery $query)
    {
        // send request
        $response = $this->sendQuery($query);

        // parse result
        $result = simplexml_load_string($response->getContent());

        $date = $query->date;
        $route = Entity\Schedule\Route::createFromXml($result, $date, null);
        return $route;
    }
}
