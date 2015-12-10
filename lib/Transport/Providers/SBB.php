<?php

namespace Transport\Providers;

class SBB extends Provider
{
    public $URL = 'http://fahrplan.sbb.ch/bin/extxml.exe/';
    public $URL_QUERY = 'http://fahrplan.sbb.ch/bin/query.exe/eny';
    public $REQ_PROD = 'iPhone3.1';
    public $API_VERSION = '2.3';
    public $ACCESS_ID = 'vWjygiRIy0uclbLz4qDO7S3G4dcIIViwoLFCZlopGhe88vlsfedGIqctZP9lvqb';
    public $SEC_ACCESS_ID = 'YJpyuPISerpXNNRTo50fNMP0yVu7L6IMuOaBgS0Xz89l3f6I3WhAjnto4kS9oz1';

    public static function isNearByLocal() {
        return true;
    }

    public static function getShortCategory($category)
    {
        $category = strtoupper($category);

        if ("T" == $category)
            return 'T';
        if ("NB" == $category)
            return 'B';
        if ("M" == $category)
            return 'M'; // Lausanne subway

        return parent::getShortCategory($category);
    }
}
