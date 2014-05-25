<?php

namespace Transport\Entity\Schedule;


use Transport\Entity\Location\Station;

class Route
{
    /**
     * @var array
     */
    public $passList = array();
    private $station;
    private $datetime;

    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Station $station)
    {
        $obj = new Route();
        $obj->station = $station;

        if ($xml->getName() == 'Journey')
            $obj->createFromStXML($xml, $date, $obj);
        else
            $obj->createFromExtXML($xml, $date, $obj);

        // Check the interval between the departure/arrival from the stops, if not the same, subtract one day
        if ($obj->datetime)
        {
            // TODO: report to admin
            $interval = date_diff($date, $obj->datetime);
            if ($interval->d > 0 || $interval->h > 0) {
                $obj->decrementOneDay();
            }
        }
        return $obj;
    }

    private function createFromExtXML(\SimpleXMLElement $xml, \DateTime $date)
    {
        $journey = $xml->JourneyRes->Journey;
        if ($journey != null) {
            foreach ($journey->PassList->children() as $stop) {

                // Skip station if no departure/arrival
                try {
                    $stop = Stop::createFromXml($stop, $date, null);
                    $this->passList[] = $stop;
                    if ($stop->station->id === $this->station->id)
                        $this->datetime = $stop->departure;

                } catch (\Exception $e) { }
            }
        }
    }

    private function createFromStXML(\SimpleXMLElement $xml, \DateTime $date)
    {
        $prevDeparture = null;
        foreach ($xml->St as $stop) {
            try {
                $stop = Stop::createFromRouteXml($stop, $date, null, $prevDeparture);
                // Skip station if no departure/arrival
                if (!$stop->isEmpty()) {
                    $prevDeparture = $stop->departure;
                    $this->passList[] = $stop;
                    if ($stop->station->id === ltrim($this->station->id, '0'))
                        $this->datetime = $stop->departure;
                }
            } catch (\Exception $e) { }
        }
    }

    private function decrementOneDay() {
        foreach($this->passList as $stop) {
            if ($stop->arrival)
                $stop->arrival->sub(new \DateInterval('P1D'));
            if ($stop->departure)
                $stop->departure->sub(new \DateInterval('P1D'));
            if ($stop->prognosis->arrival)
                $stop->prognosis->arrival->sub(new \DateInterval('P1D'));
            if ($stop->prognosis->departure)
                $stop->prognosis->departure->sub(new \DateInterval('P1D'));
        }
    }
}