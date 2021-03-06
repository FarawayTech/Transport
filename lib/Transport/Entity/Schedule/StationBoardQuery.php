<?php

namespace Transport\Entity\Schedule;

use Buzz\Browser;
use Buzz\Message\Response;
use Transport\Entity\Query;
use Transport\Entity\Transportations;
use Transport\Entity\Location\Station;

class StationBoardQuery extends Query
{
    /**
     * @var Station
     */
    public $station;
    public $boardType = 'DEP';
    public $maxJourneys = 40;
    public $date;
    public $transportations = array('all');

    public function __construct(Station $station, \DateTime $date = null)
    {
        $this->station = $station;

        if (!($date instanceof \DateTime)) {
            $date = new \DateTime('now', new \DateTimeZone('Europe/Zurich'));
        }
        $this->date = $date;
    }

    public function getQueryURL() {
        if ($this->provider->URL == null) {
            return $this->provider->STB_URL;
        }
        return $this->provider->URL;
    }

    public function toXml()
    {
        $request = $this->createRequest('STBReq');

        $board = $request->addChild('STBReq');

        $board->addAttribute('boardType', $this->boardType);
        $board->addAttribute('maxJourneys', $this->maxJourneys);
        $board->addChild('Time', $this->date->format('H:i'));

        $period = $board->addChild('Period');
        $dateBegin = $period->addChild('DateBegin');
        $dateBegin->addChild('Date', $this->date->format('Ymd'));
        $dateEnd = $period->addChild('DateEnd');
        $dateEnd->addChild('Date', $this->date->format('Ymd'));

        $tableStation = $board->addChild('TableStation');
        $tableStation->addAttribute('externalId', $this->station->id);
        $board->addChild('ProductFilter', Transportations::reduceTransportations($this->transportations));

        return $request->asXML();
    }
    
    public function toArray()
    {
        return array(
            'boardType' => strtolower($this->boardType),
            'start' => 'yes',
            'L' => 'vs_java3',
            'date' => $this->date->format('d.m.Y'),
            'time' => $this->date->format('H:i'),
            'maxJourneys' => $this->maxJourneys,
            'input' => $this->station->id,
            'productsFilter' => Transportations::reduceTransportations($this->transportations)
        );
    }

    public function __sendQuery(Browser $browser)
    {
        if ($this->supportsExtXML)
            return parent::__sendQuery($browser);
        else {
            $url = $this->getQueryURL() . '?' . http_build_query($this->toArray());
            return $browser->get($url);
        }
    }


}
