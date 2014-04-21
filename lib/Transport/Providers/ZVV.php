<?php

namespace Transport\Providers;

class ZVV extends Base
{
    protected  $STB_URL = 'http://online.fahrplan.zvv.ch/bin/stboard.exe/en';
    protected  $URL_QUERY = 'http://online.fahrplan.zvv.ch/bin/query.exe/eny';
}
