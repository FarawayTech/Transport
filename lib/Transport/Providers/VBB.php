<?php

namespace Transport\Providers;

class VBB extends Provider
{
    public  $STB_URL = 'http://fahrinfo.vbb.de/hafas/stboard.exe/en';
    public  $URL_QUERY = 'http://fahrinfo.vbb.de/hafas/query.exe/eny';

    public static function cleanRouteXML($content)
    {
        $content = parent::cleanRouteXML($content);
        $content .= '</StJourney>';
        $content = '<?xml version="1.0" encoding="ISO-8859-1"?><StJourney>'. $content;
        return $content;
    }

    public static function intToProduct($class)
    {
        if ($class == 1)
            return 'S';
        if ($class == 2)
            return 'U';
        if ($class == 4)
            return 'T';
        if ($class == 8)
            return 'B';
        if ($class == 16)
            return 'F';
        if ($class == 32)
            return 'I';
        if ($class == 64)
            return 'R';
        return 0;
    }
}
