<?php

namespace Transport\Entity\Schedule;

use Transport\Entity;

/**
 * Basic Stop
 */
class Stop
{
    public $station;

    public $arrival;
    public $departure;

    public $delay;

    public $platform;

    public $prognosis;

    public function __construct()
    {
        $this->prognosis = new Prognosis();
    }

    /**
     * Calculates a datetime by parsing the time and date given
     *
     * @param   string		$time		The time to parse, can contain an optional offset prefix (e.g., "02d")
     * @param   \DateTime	$date       The date
     * @return  \DateTime  The parsed time in ISO format
     */
    static public function calculateDateTime($time, \DateTime $date)
    {
        $offset = 0;
        if (substr($time, 2, 1) == 'd') {
            $offset = substr($time, 0, 2);
            $time = substr($time, 3);
        }
        // Prevent changing the reference
        $date = clone $date;
        $date->add(new \DateInterval('P' . $offset . 'D'));
        $timeObj = \DateTime::createFromFormat('H:i:s', $time, $date->getTimezone());
        if ($timeObj === false) {
            $timeObj = \DateTime::createFromFormat('H:i', $time, $date->getTimezone());
        }
        $date->setTime($timeObj->format('H'), $timeObj->format('i'), $timeObj->format('s'));

        return $date;
    }

    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Stop $obj = null)
    {
        if (!$obj) {
            $obj = new Stop();
        }

        $obj->station = Entity\Location\Station::createFromXml($xml->Station); // deprecated, use location instead

        foreach ($xml->children() as $location) {

            $location = Entity\LocationFactory::createFromXml($location);
            if ($location) {
                $obj->location = $location;
                break;
            }
        }

        if ($xml->Arr) {
            $obj->arrival = self::calculateDateTime((string) $xml->Arr->Time, $date);
            $obj->platform = trim((string) $xml->Arr->Platform->Text);
        }
        if ($xml->Dep) {
            $obj->departure = self::calculateDateTime((string) $xml->Dep->Time, $date);
            $obj->platform = trim((string) $xml->Dep->Platform->Text);
        }
        $obj->prognosis = Prognosis::createFromXml($xml->StopPrognosis, $obj->arrival, $obj->departure);

        if ($obj->prognosis) {
            if ($obj->prognosis->arrival && $obj->arrival) {
                $obj->delay = ($obj->prognosis->arrival->getTimestamp() - $obj->arrival->getTimestamp()) / 60;
            }
            if ($obj->prognosis->departure && $obj->departure) {
                $obj->delay = ($obj->prognosis->departure->getTimestamp() - $obj->departure->getTimestamp()) / 60;
            }
        }

        return $obj;
    }

    static public function createFromRouteXml(\SimpleXMLElement $xml, \DateTime $date, Stop $obj = null, $prevTime = null)
    {
        if (!$obj) {
            $obj = new Stop();
        }

        $obj->station = Entity\Location\Station::createFromStXml($xml); // deprecated, use location instead
        $obj->location = $obj->station;
        $adelay = 0;
        $ddelay = 0;

        if ($xml['arrTime']) {
            $obj->arrival = self::calculateDateTime((string) $xml['arrTime'], $date);
            if ($prevTime) {
                if ($prevTime > $obj->arrival)
                    $obj->arrival->add(new \DateInterval('P1D'));
            }
            $adelay = (int) $xml['adelay'];
        }
        if ($xml['depTime']) {
            $obj->departure = self::calculateDateTime((string) $xml['depTime'], $date);
            if ($prevTime) {
                if ($prevTime > $obj->departure)
                    $obj->departure->add(new \DateInterval('P1D'));
            }
            $ddelay = (int) $xml['ddelay'];
        }
        $obj->platform = trim((string) $xml['platform']);

        if ($adelay) {
            $obj->prognosis->arrival = clone $obj->arrival;
            $obj->prognosis->arrival= $obj->prognosis->arrival->add(new \DateInterval('PT'.$adelay.'M'));
            $obj->delay = $adelay;
        }
        if ($ddelay) {
            $obj->prognosis->departure = clone $obj->departure;
            $obj->prognosis->departure= $obj->prognosis->departure->add(new \DateInterval('PT'.$ddelay.'M'));
            $obj->delay = $ddelay;
        }

        return $obj;
    }

    public static function createFromStbXml(\SimpleXMLElement $xml, Entity\Location\Station $station)
    {
        $obj = new Stop();
        $obj->station = $station;
        $obj->location = $station;

        $date = \DateTime::createFromFormat('d.m.y', (string)$xml['fpDate']);
        $obj->departure = self::calculateDateTime((string) $xml['fpTime'], $date);
        $delay = (int)$xml['e_delay'];

        if ($xml['platform'])
            $obj->platform = trim((string) $xml['platform']);
        $obj->prognosis = new Prognosis();

        if ($delay) {
            $obj->prognosis->departure = clone $obj->departure;
            $obj->prognosis->departure= $obj->prognosis->departure->add(new \DateInterval('PT'.$delay.'M'));
            $obj->delay = $delay;
        }
        if ($xml['newpl'])
            $obj->prognosis->platform = trim((string) $xml['newpl']);

        return $obj;
    }

    public function isEmpty() {
        if ($this->arrival)
            return false;
        if ($this->departure)
            return false;
        return true;
    }
}
