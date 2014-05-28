<?php

namespace Transport\Providers;

class Provider
{
    public $URL;
    public $URL_QUERY;
    public $STB_URL;

    public $REQ_PROD = 'hafas';
    public $API_VERSION = '1.1';
    public $ACCESS_ID;

    const SEARCH_MODE_NORMAL = 'N';
    const SEARCH_MODE_ECONOMIC = 'P';

    public static $DEST_ATTR1 = 'dir';
    public static $DEST_ATTR2 = 'targetLoc';

    public static function getProvider($country, $area, $locality)
    {
        // http://rmv.hafas.de/bin/query.exe/en - Rhein am Main, supports vs_java3 for stboard.exe
        $provider = new SBB();
        if ($country == 'CH') {
            if ($area == 'Zurich')
                $provider = new ZVV();
        } else if ($country == 'DE'){
            if($locality=='Berlin')
                $provider = new VBB();
        } else if ($country=='AT') {
            //$provider = new OEBB();
        }
        return $provider;
    }

    public static function intToProduct($class)
    {
        return 0;
    }

    private static function cleanHafasXML($content) {
        $content = str_replace(" & ", " &amp; ", $content);
        $content = str_replace("<b>", " ", $content);
        $content = str_replace("</b>", " ", $content);
        $content = str_replace("<u>", " ", $content);
        $content = str_replace("</u>", " ", $content);
        $content = str_replace("<br />", " ", $content);
        $content = str_replace("<br>", " ", $content);
        $content = str_replace(" ->", " &#x2192;", $content);
        $content = str_replace(" <-", " &#x2190;", $content);
        $content = str_replace(" <> ", " &#x2194; ", $content);
        $content = str_replace("&nbsp;", " ", $content);
        return $content;
    }

    public static function cleanStbXML($content) {
        $content = self::cleanHafasXML($content);
        return $content;
    }

    public static function cleanRouteXML($content) {
        $content = self::cleanHafasXML($content);
        return $content;
    }

    public static function getShortCategory($category)
    {
        $category = strtoupper($category);

        // Intercity
        if ("EC" == $category) // EuroCity
            return 'I';
        if ("EN" == $category) // EuroNight
            return 'I';
        if ("D" == $category) // EuroNight, Sitzwagenabteil
            return 'I';
        if ("EIC" == $category) // Ekspres InterCity, Polen
            return 'I';
        if ("ICE" == $category) // InterCityExpress
            return 'I';
        if ("IC" == $category) // InterCity
            return 'I';
        if ("ICT" == $category) // InterCity
            return 'I';
        if ("ICN" == $category) // Intercity-Neigezug, Schweiz
            return 'I';
        if ("CNL" == $category) // CityNightLine
            return 'I';
        if ("OEC" == $category) // ÖBB-EuroCity
            return 'I';
        if ("OIC" == $category) // ÖBB-InterCity
            return 'I';
        if ("RJ" == $category) // RailJet, Österreichische Bundesbahnen
            return 'I';
        if ("WB" == $category) // westbahn
            return 'I';
        if ("THA" == $category) // Thalys
            return 'I';
        if ("TGV" == $category) // Train à Grande Vitesse
            return 'I';
        if ("DNZ" == $category) // Nachtzug Basel-Moskau
            return 'I';
        if ("AIR" == $category) // Generic Flight
            return 'I';
        if ("ECB" == $category) // EC, Verona-München
            return 'I';
        if ("LYN" == $category) // Dänemark
            return 'I';
        if ("NZ" == $category) // Schweden, Nacht
            return 'I';
        if ("INZ" == $category) // Nacht
            return 'I';
        if ("RHI" == $category) // ICE
            return 'I';
        if ("RHT" == $category) // TGV
            return 'I';
        if ("TGD" == $category) // TGV
            return 'I';
        if ("IRX" == $category) // IC
            return 'I';
        if ("ES" == $category) // Eurostar Italia
            return 'I';
        if ("EST" == $category) // Eurostar Frankreich
            return 'I';
        if ("EM" == $category) // Euromed, Barcelona-Alicante, Spanien
            return 'I';
        if ("A" == $category) // Spain, Highspeed
            return 'I';
        if ("AVE" == $category) // Alta Velocidad Española, Spanien
            return 'I';
        if ("ARC" == $category) // Arco (Renfe), Spanien
            return 'I';
        if ("ALS" == $category) // Alaris (Renfe), Spanien
            return 'I';
        if ("ATR" == $category) // Altaria (Renfe), Spanien
            return 'R';
        if ("TAL" == $category) // Talgo, Spanien
            return 'I';
        if ("TLG" == $category) // Spanien, Madrid
            return 'I';
        if ("HOT" == $category) // Spanien, Nacht
            return 'I';
        if ("X2" == $category) // X2000 Neigezug, Schweden
            return 'I';
        if ("X" == $category) // InterConnex
            return 'I';
        if ("FYR" == $category) // Fyra, Amsterdam-Schiphol-Rotterdam
            return 'I';
        if ("FYRA" == $category) // Fyra, Amsterdam-Schiphol-Rotterdam
            return 'I';
        if ("SC" == $category) // SuperCity, Tschechien
            return 'I';
        if ("LE" == $category) // LEO Express, Prag
            return 'I';
        if ("FLUG" == $category)
            return 'I';
        if ("TLK" == $category) // Tanie Linie Kolejowe, Polen
            return 'I';
        if ("INT" == $category) // Zürich-Brüssel - Budapest-Istanbul
            return 'I';
        if ("HKX" == $category) // Hamburg-Koeln-Express
            return 'I';

        // Regional
        if ("ZUG" == $category) // Generic Train
            return 'R';
        if ("R" == $category) // Generic Regional Train
            return 'R';
        if ("DPN" == $category) // Dritter Personen Nahverkehr
            return 'R';
        if ("RB" == $category) // RegionalBahn
            return 'R';
        if ("RE" == $category) // RegionalExpress
            return 'R';
        if ("IR" == $category) // Interregio
            return 'R';
        if ("IRE" == $category) // Interregio Express
            return 'R';
        if ("HEX" == $category) // Harz-Berlin-Express, Veolia
            return 'R';
        if ("WFB" == $category) // Westfalenbahn
            return 'R';
        if ("RT" == $category) // RegioTram
            return 'R';
        if ("REX" == $category) // RegionalExpress, Österreich
            return 'R';
        if ("OS" == $category) // Osobný vlak, Slovakia oder Osobní vlak, Czech Republic
            return 'R';
        if ("SP" == $category) // Spěšný vlak, Czech Republic
            return 'R';
        if ("EZ" == $category) // ÖBB ErlebnisBahn
            return 'R';
        if ("ARZ" == $category) // Auto-Reisezug Brig - Iselle di Trasquera
            return 'R';
        if ("OE" == $category) // Ostdeutsche Eisenbahn
            return 'R';
        if ("MR" == $category) // Märkische Regionalbahn
            return 'R';
        if ("PE" == $category) // Prignitzer Eisenbahn GmbH
            return 'R';
        if ("NE" == $category) // NEB Betriebsgesellschaft mbH
            return 'R';
        if ("MRB" == $category) // Mitteldeutsche Regiobahn
            return 'R';
        if ("ERB" == $category) // eurobahn (Keolis Deutschland)
            return 'R';
        if ("HLB" == $category) // Hessische Landesbahn
            return 'R';
        if ("VIA" == $category)
            return 'R';
        if ("HSB" == $category) // Harzer Schmalspurbahnen
            return 'R';
        if ("OSB" == $category) // Ortenau-S-Bahn
            return 'R';
        if ("VBG" == $category) // Vogtlandbahn
            return 'R';
        if ("AKN" == $category) // AKN Eisenbahn AG
            return 'R';
        if ("OLA" == $category) // Ostseeland Verkehr
            return 'R';
        if ("UBB" == $category) // Usedomer Bäderbahn
            return 'R';
        if ("PEG" == $category) // Prignitzer Eisenbahn
            return 'R';
        if ("NWB" == $category) // NordWestBahn
            return 'R';
        if ("CAN" == $category) // cantus Verkehrsgesellschaft
            return 'R';
        if ("BRB" == $category) // ABELLIO Rail
            return 'R';
        if ("SBB" == $category) // Schweizerische Bundesbahnen
            return 'R';
        if ("VEC" == $category) // vectus Verkehrsgesellschaft
            return 'R';
        if ("TLX" == $category) // Trilex (Vogtlandbahn)
            return 'R';
        if ("HZL" == $category) // Hohenzollerische Landesbahn
            return 'R';
        if ("ABR" == $category) // Bayerische Regiobahn
            return 'R';
        if ("CB" == $category) // City Bahn Chemnitz
            return 'R';
        if ("WEG" == $category) // Württembergische Eisenbahn-Gesellschaft
            return 'R';
        if ("NEB" == $category) // Niederbarnimer Eisenbahn
            return 'R';
        if ("ME" == $category) // metronom Eisenbahngesellschaft
            return 'R';
        if ("MER" == $category) // metronom regional
            return 'R';
        if ("ALX" == $category) // Arriva-Länderbahn-Express
            return 'R';
        if ("EB" == $category) // Erfurter Bahn
            return 'R';
        if ("EBX" == $category) // Erfurter Bahn
            return 'R';
        if ("VEN" == $category) // Rhenus Veniro
            return 'R';
        if ("BOB" == $category) // Bayerische Oberlandbahn
            return 'R';
        if ("SBS" == $category) // Städtebahn Sachsen
            return 'R';
        if ("SES" == $category) // Städtebahn Sachsen Express
            return 'R';
        if ("EVB" == $category) // Eisenbahnen und Verkehrsbetriebe Elbe-Weser
            return 'R';
        if ("STB" == $category) // Süd-Thüringen-Bahn
            return 'R';
        if ("AG" == $category) // Ingolstadt-Landshut
            return 'R';
        if ("PRE" == $category) // Pressnitztalbahn
            return 'R';
        if ("DBG" == $category) // Döllnitzbahn GmbH
            return 'R';
        if ("SHB" == $category) // Schleswig-Holstein-Bahn
            return 'R';
        if ("NOB" == $category) // Nord-Ostsee-Bahn
            return 'R';
        if ("RTB" == $category) // Rurtalbahn
            return 'R';
        if ("BLB" == $category) // Berchtesgadener Land Bahn
            return 'R';
        if ("NBE" == $category) // Nordbahn Eisenbahngesellschaft
            return 'R';
        if ("SOE" == $category) // Sächsisch-Oberlausitzer Eisenbahngesellschaft
            return 'R';
        if ("SDG" == $category) // Sächsische Dampfeisenbahngesellschaft
            return 'R';
        if ("VE" == $category) // Lutherstadt Wittenberg
            return 'R';
        if ("DAB" == $category) // Daadetalbahn
            return 'R';
        if ("WTB" == $category) // Wutachtalbahn e.V.
            return 'R';
        if ("BE" == $category) // Grensland-Express
            return 'R';
        if ("ARR" == $category) // Ostfriesland
            return 'R';
        if ("HTB" == $category) // Hörseltalbahn
            return 'R';
        if ("FEG" == $category) // Freiberger Eisenbahngesellschaft
            return 'R';
        if ("NEG" == $category) // Norddeutsche Eisenbahngesellschaft Niebüll
            return 'R';
        if ("RBG" == $category) // Regental Bahnbetriebs GmbH
            return 'R';
        if ("MBB" == $category) // Mecklenburgische Bäderbahn Molli
            return 'R';
        if ("VEB" == $category) // Vulkan-Eifel-Bahn Betriebsgesellschaft
            return 'R';
        if ("LEO" == $category) // Chiemgauer Lokalbahn
            return 'R';
        if ("VX" == $category) // Vogtland Express
            return 'R';
        if ("MSB" == $category) // Mainschleifenbahn
            return 'R';
        if ("P" == $category) // Kasbachtalbahn
            return 'R';
        if ("ÖBA" == $category) // Öchsle-Bahn Betriebsgesellschaft
            return 'R';
        if ("KTB" == $category) // Kandertalbahn
            return 'R';
        if ("ERX" == $category) // erixx
            return 'R';
        if ("ATZ" == $category) // Autotunnelzug
            return 'R';
        if ("ATB" == $category) // Autoschleuse Tauernbahn
            return 'R';
        if ("CAT" == $category) // City Airport Train
            return 'R';
        if ("EXTRA" == $category or "EXT" == $category) // Extrazug
            return 'R';
        if ("KD" == $category) // Koleje Dolnośląskie (Niederschlesische Eisenbahn)
            return 'R';
        if ("KM" == $category) // Koleje Mazowieckie
            return 'R';
        if ("EX" == $category) // Polen
            return 'R';
        if ("PCC" == $category) // PCC Rail, Polen
            return 'R';
        if ("ZR" == $category) // ZSR (Slovakian Republic Railways)
            return 'R';
        if ("RNV" == $category) // Rhein-Neckar-Verkehr GmbH
            return 'R';
        if ("DWE" == $category) // Dessau-Wörlitzer Eisenbahn
            return 'R';
        if ("BKB" == $category) // Buckower Kleinbahn
            return 'R';
        if ("GEX" == $category) // Glacier Express
            return 'R';
        if ("M" == $category) // Meridian
            return 'R';
        if ("WBA" == $category) // Waldbahn
            return 'R';
        if ("BEX" == $category) // Bernina Express
            return 'R';
        if ("VAE" == $category) // Voralpen-Express
            return 'R';

        // Suburban Trains
        if (preg_match('/^SN?\d*$/', $category)) // Generic (Night) S-Bahn
            return 'S';
        if ("S-BAHN" == $category)
            return 'S';
        if ("BSB" == $category) // Breisgau S-Bahn
            return 'S';
        if ("SWE" == $category) // Südwestdeutsche Verkehrs-AG, Ortenau-S-Bahn
            return 'S';
        if ("RER" == $category) // Réseau Express Régional, Frankreich
            return 'S';
        if ("WKD" == $category) // Warszawska Kolej Dojazdowa (Warsaw Suburban Railway)
            return 'S';
        if ("SKM" == $category) // Szybka Kolej Miejska Tricity
            return 'S';
        if ("SKW" == $category) // Szybka Kolej Miejska Warschau
            return 'S';
        // if ("SPR" == normalizedType)) // Sprinter, Niederlande
        // return "S" + normalizedName;

        // Subway
        if ("U" == $category) // Generic U-Bahn
            return 'U';
        if ("MET" == $category)
            return 'U';
        if ("METRO" == $category)
            return 'U';

        // Tram
        if (preg_match('/STR\w{0,5}/', $category)) // Generic Tram
            return 'T';
        if ("NFT" == $category) // Niederflur-Tram
            return 'T';
        if ("TRAM" == $category)
            return 'T';
        if ("TRA" == $category)
            return 'T';
        if ("WLB" == $category) // Wiener Lokalbahnen
            return 'T';
        if ("STRWLB" == $category) // Wiener Lokalbahnen
            return 'T';
        if ("SCHW-B" == $category) // Schwebebahn, gilt als "Straßenbahn besonderer Bauart"
            return 'T';

        // Bus
        if (preg_match('/BUS\w{0,5}/', $category)) // Generic Bus
            return 'B';
        if ("NFB" == $category) // Low-floor bus (Niederflur-Bus)
            return 'B';
        if ("SEV" == $category) // Schienen-Ersatz-Verkehr
            return 'B';
        if ("BUSSEV" == $category) // Schienen-Ersatz-Verkehr
            return 'B';
        if ("BSV" == $category) // Bus SEV
            return 'B';
        if ("FB" == $category) // Fernbus? Luxemburg-Saarbrücken
            return 'B';
        if ("EXB" == $category) // Expressbus München-Prag?
            return 'B';
        if ("NFO" == $category) // Low-floor trolleybus (Niederflur-Omnibus)
            return 'B';
        if ("TRO" == $category) // Trolleybus
            return 'B';
        if ("RFB" == $category) // Rufbus
            return 'B';
        if ("RUF" == $category) // Rufbus
            return 'B';
        if (preg_match('/TAX\w{0,5}/', $category)) // Generic Taxi
            return 'B';
        if ("RFT" == $category) // Ruftaxi
            return 'B';
        if ("LT" == $category) // Linien-Taxi
            return 'B';
        // if ("N" == normalizedType)) // Nachtbus
        // return "B" + normalizedName;

        // Phone
        if (substr($category, 0, 3) == "AST") // Anruf-Sammel-Taxi
            return 'P';
        if (substr($category, 0, 3) == "ALT") // Anruf-Linien-Taxi
            return 'P';
        if (substr($category, 0, 4) == "BUXI") // Bus-Taxi (Schweiz)
            return 'P';

        // Ferry
        if ("SCHIFF" == $category)
            return 'F';
        if ("FÄHRE" == $category)
            return 'F';
        if ("FÄH" == $category)
            return 'F';
        if ("FAE" == $category)
            return 'F';
        if ("SCH" == $category) // Schiff
            return 'F';
        if ("AS" == $category) // SyltShuttle, eigentlich Autoreisezug
            return 'F';
        if ("KAT" == $category) // Katamaran, e.g. Friedrichshafen - Konstanz
            return 'F';
        if ("BAT" == $category) // Boots Anlege Terminal?
            return 'F';
        if ("BAV" == $category) // Boots Anlege?
            return 'F';

        // Cable Car
        if ("SEILBAHN" == $category)
            return 'C';
        if ("SB" == $category) // Seilbahn
            return 'C';
        if ("ZAHNR" == $category) // Zahnradbahn, u.a. Zugspitzbahn
            return 'C';
        if ("GB" == $category) // Gondelbahn
            return 'C';
        if ("LB" == $category) // Luftseilbahn
            return 'C';
        if ("FUN" == $category) // Funiculaire (Standseilbahn)
            return 'C';
        if ("SL" == $category) // Sessel-Lift
            return 'C';

        return 0;
    }
}
