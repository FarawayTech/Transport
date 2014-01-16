<?php

namespace Transport\Entity\Schedule;


class Route
{
    /**
     * @var array
     */
    public $passList = array();

    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Route $obj = null)
    {
        if (!$obj) {
            $obj = new Route();
        }

        $journey = $xml->JourneyRes->Journey;
        if ($journey != null) {
            foreach ($journey->PassList->children() as $stop) {
                $stop = Stop::createFromXml($stop, $date, null);
                $obj->passList[] = $stop;
            }
        }
        return $obj;
    }
}