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
    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Provider $provider, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new StationBoardJourney();
        }

        $obj = Journey::createFromXml($xml, $date, $provider, $obj);

        $obj->stop = Stop::createFromXml($xml->MainStop->BasicStop, $date, null);

        return $obj;
    }

    static public function createFromStbXml(\SimpleXMLElement $xml, Provider $provider, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new StationBoardJourney();
        }

        $obj = Journey::createFromStbXml($xml, $provider, $obj);

        return $obj;
    }

    public static function createListFromXml(\SimpleXMLElement $xml, \DateTime $date, Provider $provider)
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
                $status = trim((string)$journey->JProg->JStatus);
                if ($status == "FAILURE")
                    continue;
                $journeys[] = self::createFromXml($journey, $localDate, $provider, null);
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
                if ($delay != 'cancel') {
                    $journey_obj = self::createFromStbXml($journey, $provider);
                    $journey_obj->stop = Stop::createFromStbXml($journey, $station);
                    $journeys[] = $journey_obj;
                }
            }
        }
        return $journeys;
    }
}
