<?php

namespace Transport\Entity\Schedule;

use Transport\Entity\Query;
use Transport\Entity\Location\Station;
use Transport\Entity\Transportations;

class RouteQuery extends Query
{
    public $boardType = 'DEP';

    public $station;

    public $date;

    public $jHandle;


    public function __construct(Station $station, $jHandle, \DateTime $date = null)
    {

        $this->station = $station;

        if (!($date instanceof \DateTime)) {
            $date = new \DateTime('now', new \DateTimeZone('Europe/Zurich'));
        }
        $this->date = $date;
        $this->jHandle = $jHandle;
    }

    public function getQueryURL() {
        if ($this->provider->URL == null) {
            return $this->provider->STB_URL;
        }
        return $this->provider->URL;
    }

    public function toXml()
    {
       //<JourneyReq date="20130902" time="22:42" type="DEP" externalId="008577784#95">
       //<JHandle tNr="169624" puic="095" cycle="11"></JHandle></JourneyReq>

        $request = $this->createRequest();

        $journey = $request->addChild('JourneyReq');
        $journey->addAttribute('date', $this->date->format('Ymd'));
        $journey->addAttribute('externalId', $this->station->id);
        $journey->addAttribute('time', $this->date->format('H:i'));
        $journey->addAttribute('type', $this->boardType);

        $jhandle_arr = explode(";", $this->jHandle);
        $jhandle = $journey->addChild('JHandle');
        $jhandle->addAttribute('tNr', $jhandle_arr[0]);
        $jhandle->addAttribute('cycle', $jhandle_arr[2]);
        $jhandle->addAttribute('puic', $jhandle_arr[1]);

        return $request->asXML();

    }

    public function toArray()
    {
        $jhandle_arr = explode(";", $this->jHandle);
        return array(
            'sTI' => '1',
            'start' => 'yes',
            'L' => 'vs_java3',
            'date' => $this->date->format('d.m.Y'),
            'time' => $this->date->format('H:i'),
            'REQTrain_name' => $jhandle_arr[0],
            'dirInput' => $jhandle_arr[1],
            'inputTripelId' => 'L='.$this->station->id
        );
    }
}
