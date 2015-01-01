<?php

namespace Transport\Entity\Schedule;
use Transport\Providers\Provider;

/**
 * Request for a station board journey
 */
class StationBoardJourney extends Journey
{
    public static function createListFromXml(\SimpleXMLElement $xml, \DateTime $date, $lines, Provider $provider)
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
                $journeys[] = self::createFromXml($journey, $localDate, $lines, $provider, null);
                $prevTime = $curTime;
            }
        }
        return $journeys;
    }


    public static function createListFromStbXml(\SimpleXMLElement $xml, $lines, Provider $provider)
    {
        $journeys = array();
        if ($xml->Journey) {
            foreach ($xml->Journey as $journey) {
                $delay = (string)$journey['delay'];
                if ($delay != 'cancel') {
                    $journeys[] = self::createFromStbXml($journey, $lines, $provider);
                }
            }
        }
        return $journeys;
    }
}
