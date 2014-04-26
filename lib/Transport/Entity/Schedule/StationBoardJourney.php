<?php

namespace Transport\Entity\Schedule;
use Transport\Entity\Location\Station;
use Transport\Providers\Provider;

/**
 * Request for a station board journey
 */
class StationBoardJourney extends Journey
{
    /**
     * @var Transport\Entity\Schedule\Stop
     */
    public $stop;

    /**
     * @param   \SimpleXMLElement   $xml
     * @param   string              $date   The date that will be assigned to this journey
     * @param   Journey             $obj    An optional existing journey to overwrite
     * @return  Journey
     */
    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new StationBoardJourney();
        }

        $obj = Journey::createFromXml($xml, $date, $obj);

        $obj->stop = Stop::createFromXml($xml->MainStop->BasicStop, $date, null);

        return $obj;
    }

    static public function createFromStbXml(\SimpleXMLElement $xml, Station $station, Provider $provider, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new StationBoardJourney();
        }

        $obj = Journey::createFromStbXml($xml, $station, $provider, $obj);

        $obj->stop = Stop::createFromStbXml($xml, $station);

        return $obj;
    }

    public static function createListFromXml(\SimpleXMLElement $xml, \DateTime $date)
    {
        // since the stationboard always lists all connections starting from now we just use the date
        // and wrap it accordingly if time goes over midnight
        $localDate = clone $date;
        $journeys = array();
        // subtract one minute because SBB also returns results for one minute in the past
        $prevTime = time() - 60;
        if ($xml->STBRes->JourneyList->STBJourney) {
            foreach ($xml->STBRes->JourneyList->STBJourney as $journey) {
                $curTime = strtotime((string) $journey->MainStop->BasicStop->Dep->Time);
                $prognosis = strtotime((string) $journey->MainStop->BasicStop->StopPrognosis->Dep->Time);
                if (!$prognosis)
                    $prognosis = $curTime;
                if ($prevTime > $curTime && $prevTime > $prognosis) { // we passed midnight
                    $localDate->add(new \DateInterval('P1D'));
                }
                $journeys[] = self::createFromXml($journey, $localDate, null);
                $prevTime = $curTime;
            }
        }
        return $journeys;
    }


    public static function createListFromStbXml(\SimpleXMLElement $xml, Station $station, Provider $provider)
    {
        $journeys = array();
        if ($xml->Journey) {
            foreach ($xml->Journey as $journey) {
                $delay = (string)$journey['delay'];
                if ($delay != 'cancel')
                    $journeys[] = self::createFromStbXml($journey, $station, $provider);
            }
        }
        return $journeys;
    }
}
