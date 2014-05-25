<?php

namespace Transport\Providers;

class OEBB extends Provider
{
    public $STB_URL = 'http://fahrplan.oebb.at/bin/stboard.exe/en';
    public $URL_QUERY = 'http://fahrplan.oebb.at/bin/query.exe/eny';

    public static function intToProduct($class)
    {
        if ($class == 1)
            return 'I';
        if ($class == 2)
            return 'I';
        if ($class == 4)
            return 'I';
        if ($class == 8)
            return 'R';
        if ($class == 16)
            return 'R';
        if ($class == 32)
            return 'S';
        if ($class == 64)
            return 'B';
        if ($class == 128)
            return 'F';
        if ($class == 256)
            return 'U';
        if ($class == 512)
            return 'T';
        if ($class == 1024) // Autoreisezug
            return 'I';
        if ($class == 2048)
            return 'P';
        if ($class == 4096)
            return 'I';
        return 0;
    }
}
