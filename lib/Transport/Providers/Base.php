<?php

namespace Transport\Providers;

class Base
{
    protected $URL;
    protected $URL_QUERY;
    protected $STB_URL;

    protected $REQ_PROD = 'hafas';
    protected $API_VERSION = '1.1';
    protected $ACCESS_ID;

    const SEARCH_MODE_NORMAL = 'N';
    const SEARCH_MODE_ECONOMIC = 'P';
}
