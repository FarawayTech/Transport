<?php 

namespace Transport\Entity;

use Transport\Providers\Provider;

abstract class Query
{
    public $lang = 'EN';
    private $provider;

    public function addProvider(Provider $provider) {
        $this->provider = $provider;
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

    public function toXml() {

        $request = $this->createRequest();
        return $request->asXML();
    }
}
