<?php

namespace Transport\Providers;

class DB extends Base
{
    protected  $STB_URL = 'http://reiseauskunft.bahn.de/bin/bhftafel.exe/en';
    protected  $URL_QUERY = 'http://reiseauskunft.bahn.de/bin/query.exe/eny';
}
