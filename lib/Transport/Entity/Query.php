<?php 

namespace Transport\Entity;

use Transport\Entity\Schedule\StationBoardQuery;
use Transport\Providers\Provider;

abstract class Query
{
    public $lang = 'EN';
    protected $provider;
    protected $supportsExtXML = true;

    public function addProvider(Provider $provider) {
        $this->provider = $provider;
        if ($provider->URL == null) {
            $this->supportsExtXML = false;
        }
    }

    public function isExtXML() {
        return $this->supportsExtXML;
    }

    public function getQueryURL() {
        if ($this->provider->URL == null) {
            return $this->provider->URL_QUERY;
        }
        return $this->provider->URL;
    }

    /**
     * @return  \SimpleXMLElement
     */
    protected function createRequest()
    {
        $request = new \SimpleXMLElement('<?xml version="1.0" encoding="iso-8859-1"?><ReqC />');
        $request['lang'] = $this->lang;

        $request['prod'] = $this->provider->REQ_PROD;
        $request['ver'] = $this->provider->API_VERSION;
        $request['accessId'] = $this->provider->ACCESS_ID;

        return $request;
    }

    public abstract function toXml();
}
