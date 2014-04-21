<?php

namespace Transport\Providers;

class SBB extends Provider
{
    public $URL = 'http://fahrplan.sbb.ch/bin/extxml.exe/';
    public $URL_QUERY = 'http://fahrplan.sbb.ch/bin/query.exe/eny';
    public $REQ_PROD = 'iPhone3.1';
    public $API_VERSION = '2.3';
    public $ACCESS_ID = 'YJpyuPISerpXNNRTo50fNMP0yVu7L6IMuOaBgS0Xz89l3f6I3WhAjnto4kS9oz1';
}
