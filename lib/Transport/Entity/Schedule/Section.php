<?php

namespace Transport\Entity\Schedule;

use Transport\DB;
use Transport\Entity;
use Transport\Providers\Provider;

class Section
{
    /**
     * @var Entity\Schedule\Journey
     */
    public $journey;

    /**
     * @var Entity\Schedule\Walk
     */
    public $walk;

    /**
     * @var Entity\Schedule\Stop
     */
    public $departure;

    /**
     * @var Entity\Schedule\Stop
     */
    public $arrival;
    
    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Provider $provider, Section $obj = null)
    {
        if (!$obj) {
            $obj = new Section();
        }

        $obj->departure = Stop::createFromXml($xml->Departure->BasicStop, $date, null);
        $obj->arrival = Stop::createFromXml($xml->Arrival->BasicStop, $date, null);

        $coordinates = $obj->departure->station->coordinate;

        if ($xml->Journey) {
            $lines = DB::getLines($coordinates->x, $coordinates->y);
            $obj->journey = Journey::createFromXml($xml->Journey, $date, $lines, $provider, null);
        }

        if ($xml->Walk) {
            $obj->walk = Walk::createFromXml($xml->Walk, $date);
        }

        return $obj;
    }
}
