<?php

namespace Transport\Providers;

class SBB extends Base
{
    protected $URL = 'http://fahrplan.sbb.ch/bin/extxml.exe/';
    protected $URL_QUERY = 'http://fahrplan.sbb.ch/bin/query.exe/eny';
    protected $REQ_PROD = 'iPhone3.1';
    protected $API_VERSION = '2.3';
    protected $ACCESS_ID = 'YJpyuPISerpXNNRTo50fNMP0yVu7L6IMuOaBgS0Xz89l3f6I3WhAjnto4kS9oz1';
}
