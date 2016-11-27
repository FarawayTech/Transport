<?php 

namespace Transport\Entity;

use Buzz\Browser;
use Buzz\Message\Response;
use Transport\ISendQuery;
use Transport\Providers\Provider;

abstract class Query implements ISendQuery
{
    public $lang = 'EN';
    protected $provider;
    protected $supportsExtXML = true;

    private static $HEADERS = array(
        'User-Agent: SBBMobile/4.8 CFNetwork/609.1.4 Darwin/13.0.0',
        'Accept: application/xml',
        'Content-Type: application/xml');

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


    /**
     * @param \Buzz\Browser $browser
     * @return Response
     */
    public static function sendQuery(Browser $browser, ISendQuery $query)
    {
        $i = 5;
        $statusCode = 0;
        $response = null;
        // try 5 times
        while ($i > 0 and $statusCode != 200) {
            try {
                //'http://localhost:8080/timeout.php?seconds=10'
                $response = $query->__sendQuery($browser);
                $statusCode = $response->getStatusCode();
            }
            catch (\Exception $e) {
                error_log($e->getMessage());
            }
            $i--;
        }
        if ($statusCode != 200 && $response!=null) {
            error_log($response->getReasonPhrase());
            error_log($response->getContent());
        }
        return $response;
    }

    /**
     * Default implementation
     * @param \Buzz\Browser $browser
     * @return Response
     */
    public function __sendQuery(Browser $browser) {
        return $browser->post($this->getQueryURL(), Query::$HEADERS, $this->toXml());
    }
}
