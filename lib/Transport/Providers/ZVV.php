<?php

namespace Transport\Providers;

class ZVV extends Provider
{
    public $STB_URL = 'http://online.fahrplan.zvv.ch/bin/stboard.exe/en';
    public $URL_QUERY = 'http://online.fahrplan.zvv.ch/bin/query.exe/eny';

    public static function cleanRouteXML($content)
    {
        $content = parent::cleanRouteXML($content);
        $content .= '</Journey>';
        $content = str_replace('?>', '?><Journey>', $content);
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
            return 'F';
        if ($class == 32)
            return 'S';
        if ($class == 64)
            return 'B';
        if ($class == 128)
            return 'C';
        if ($class == 256)
            return 'U';
        if ($class == 512)
            return 'T';
        return 0;
    }

    public static function getShortCategory($category)
    {
        $category = strtoupper($category);

        if ("T" == $category)
            return 'T';
        if ("TRM" == $category)
            return 'T';
        if ("TRM-NF" == $category) // Low-floor tramway
            return 'T';

        if ("BUS-NF" == $category) // Low-floor bus
            return 'B';
        if ("TRO-NF" == $category) // Low-floor trolley
            return 'B';
        if ("N" == $category) // Nachtbus
            return 'B';
        if ("TX" == $category)
            return 'B';
        if ("E-BUS" == $category)
            return 'B';
        if ("TROLLEY" == $category)
            return 'B';
        if ("KB" == $category) // Minibus (Kleinbus)
            return 'B';
        if ("EE" == $category)
            return 'B';

        if ("D-SCHIFF" == $category)
            return 'F';

        if ("BERGBAHN" == $category)
            return 'C';
        if ("LSB" == $category) // Luftseilbahn
            return 'C';
        if ("SLB" == $category) // Sesselliftbahn
            return 'C';

        return parent::getShortCategory($category);
    }
}
