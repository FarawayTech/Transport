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

        if ($xml->getName() == 'StJourney')
            self::createFromStXML($xml, $date, $obj);
        else
            self::createFromExtXML($xml, $date, $obj);
        return $obj;
    }

    private static function createFromExtXML(\SimpleXMLElement $xml, \DateTime $date, Route $obj)
    {
        $journey = $xml->JourneyRes->Journey;
        if ($journey != null) {
            foreach ($journey->PassList->children() as $stop) {
                try {
                    $stop = Stop::createFromXml($stop, $date, null);
                    $obj->passList[] = $stop;
                } catch (\Exception $e) { }
            }
        }
    }

    private static function createFromStXML(\SimpleXMLElement $xml, \DateTime $date, Route $obj)
    {
        foreach ($xml->children() as $stop) {
            try {
                $stop = Stop::createFromStXml($stop, $date, null);
                $obj->passList[] = $stop;
            } catch (\Exception $e) { }
        }
    }
}