<?php

namespace Transport\Entity\Schedule;

class Prognosis
{
    public $platform;
    public $arrival;
    public $departure;
    public $capacity1st;
    public $capacity2nd;

    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $arrival, \DateTime $departure)
    {
        $obj = new Prognosis();

        if ($xml->Arr) {
            if ($xml->Arr->Platform) {
                $obj->platform = (string) $xml->Arr->Platform->Text;
            }
            if ($xml->Arr->Time) {
                $obj->arrival = Stop::calculateDateTime((string) $xml->Arr->Time, $arrival);
            }
        }

        if ($xml->Dep) {
            if ($xml->Dep->Platform) {
                $obj->platform = (string) $xml->Dep->Platform->Text;
            }
            if ($xml->Dep->Time) {
                $obj->departure = Stop::calculateDateTime((string) $xml->Dep->Time, $departure);
            }
        }


        if ($xml->Capacity1st) {
            $obj->capacity1st = (int) $xml->Capacity1st;
        }
        if ($xml->Capacity2nd) {
            $obj->capacity2nd = (int) $xml->Capacity2nd;
        }

        return $obj;
    }
}
