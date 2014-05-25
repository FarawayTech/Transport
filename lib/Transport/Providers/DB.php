<?php

namespace Transport\Providers;

class DB extends Provider
{
    public  $STB_URL = 'http://reiseauskunft.bahn.de/bin/bhftafel.exe/en';
    public  $URL_QUERY = 'http://reiseauskunft.bahn.de/bin/query.exe/eny';

    public static function cleanStbXML($content) {
        $content = parent::cleanStbXML($content);
        $content = '<?xml version="1.0" encoding="ISO-8859-1"?><StationTable>' . $content;
        $content .= '</StationTable>';
        return $content;
    }

    public static function cleanRouteXML($content)
    {
        $content = parent::cleanRouteXML($content);
        $content .= '</Journey>';
        $content = '<?xml version="1.0" encoding="ISO-8859-1"?><Journey>'. $content;
        return $content;
    }

    public static function intToProduct($class)
    {
        if ($class == 1)
            return 'I';
        if ($class == 2)
            return 'I';
        if ($class == 4)
            return 'R';
        if ($class == 8)
            return 'R';
        if ($class == 16)
            return 'S';
        if ($class == 32)
            return 'B';
        if ($class == 64)
            return 'F';
        if ($class == 128)
            return 'U';
        if ($class == 256)
            return 'T';
        if ($class == 512)
            return 'P';
        return 0;
    }
}
