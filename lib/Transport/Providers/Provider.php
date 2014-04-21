<?php

namespace Transport\Providers;

class Provider
{
    public  $URL;
    public  $URL_QUERY;
    public $STB_URL;

    public $REQ_PROD = 'hafas';
    public $API_VERSION = '1.1';
    public $ACCESS_ID;

    const SEARCH_MODE_NORMAL = 'N';
    const SEARCH_MODE_ECONOMIC = 'P';

    public static function getProvider()
    {
        return new SBB();
    }
}
