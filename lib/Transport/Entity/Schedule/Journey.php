<?php

namespace Transport\Entity\Schedule;


use Transport\Providers\Provider;

class Journey
{
    public static $JOURNEYS =
    array('BaselJourney' => array('places' => array('Basel', 'Pratteln', 'Rodersdorf', 'Dornach-Arlesheim', 'Ettingen',
                                                    'Flüh', 'Basel Wiesenplatz', 'Birsfelden', 'Allschwil', 'Riehen',
                                                    'Hüslimatt', 'Aesch', 'Aesch BL Dorf', 'St-Louis Grenze',
                                                    'Binningen', 'Basel Messeplatz', 'Basel Bankverein', 'Oberwil BL'),
                                  'colors' => array('1' => 'ff804b2f', '2' => 'ffa8834a', '3' => 'ff3c4796',
                                                    '6' => 'ff006ab0', '8' => 'ffeb6ea2', '10' => 'ffffca0a',
                                                    '11' => 'ffe30910', '14' => 'ffeb7b05', '15' => 'ff00963a',
                                                    '16' => 'ffaecc06', '17' => 'ff00a3e3', '21' => 'ff00a191')),
          'BielJourney' => array('places' => array('Biel/Bienne', 'Nidau', 'Brügg BE', 'Tüscherz'),
                                 'colors' => array('1' => 'ffd92a31', '2' => 'ff293e80', '4' => 'ffe4822e',
                                                   '5' => 'ff116630', '6' => 'ff9fc841', '7' => 'ffdf4891',
                                                   '8' => 'ff7c4391', '11' => 'ff9f2e36')),
          'FribourgJourney' => array('places' => array('Fribourg', 'Marly', 'Villars-sur-Glâne', 'Granges-Paccot',
                                                       'Givisiez', 'Rosé', 'Chésopelloz', 'Corminboeuf'),
                                     'colors' => array('1' => 'ff53B401', '2' => 'ffD83B38', '3' => 'ffFFD012',
                                                       '4' => 'ffF08B32', '5' => 'ff2F7CE3', '6' => 'ff3F2072',
                                                       '7' => 'ff833816', '8' => 'ffe33988', '9' => 'ffa83989',
                                                       '11' => 'ff0fa381')),
          'BulleJourney' => array('places' => array('Bulle', 'Morlon', 'Riaz', 'Vuadens', 'La Tour-de-Trême'),
                                  'colors' => array('201' => 'ffe31c19', '202' => 'ff27a143', '203' => 'ff0070b5')),
          'GenevaJourney' => array('places' => array('Genève', 'Carouge GE', 'Veyrier', 'Bernex', 'Lancy',
                                                     'Meyrin', 'Chêne-Bourg', 'Thônex', 'Grand-Saconnex', 'Vernier',
                                                     'Onex', 'Veigy', 'Neydens', 'Grand-Lancy', 'Jussy', 'Avusy',
                                                     'Confignon', 'Plan-les-Ouates', 'Satigny', 'Ferney', 'La Plaine'),
                                   'colors' => array('1' => 'ff663399', '10' => 'ff006633', '11' => 'ff993399',
                                                     '12' => 'ffFF9900', '14' => 'ff663399', '15' => 'ff993333',
                                                     '18' => 'ffCC3399', '19' => 'ffFFCC00', '2' => 'ffCCCC33',
                                                     '21' => 'ff663333', '22' => 'ff663399', '23' => 'ffCC3399',
                                                     '25' => 'ff805A28', '27' => 'ff009DBC', '28' => 'ffFFCC00',
                                                     '3' => 'ffCC3399', '31' => 'ff009999', '32' => 'ff666666',
                                                     '33' => 'ff009999', '34' => 'ff99CCCC', '35' => 'ff666666',
                                                     '36' => 'ff666666', '4' => 'ffCC0033', '41' => 'ff009999',
                                                     '42' => 'ff99CCCC', '43' => 'ff99CCCC', '44' => 'ff009999',
                                                     '45' => 'ff99CCCC', '46' => 'ff009999', '47' => 'ff00B0A4',
                                                     '49' => 'ff009999', '5' => 'ff0099FF', '51' => 'ff009999',
                                                     '53' => 'ff99CCCC', '54' => 'ff009999', '56' => 'ff009999',
                                                     '57' => 'ff99CCCC', '6' => 'ff0099CC', '61' => 'ffFF9BAA',
                                                     '7' => 'ff009933', '8' => 'ff993333', '9' => 'ffCC0033',
                                                     'A' => 'ffFF6600', 'B' => 'ffFF6600', 'C' => 'ffFF6600',
                                                     'D' => 'ffFF9999', 'DN' => 'ffFF9BAA', 'E' => 'ffFF6600',
                                                     'F' => 'ffFF9999', 'G' => 'ffFF9999', 'K' => 'ffFF9999',
                                                     'L' => 'ffFF6600', 'M' => 'ffFF9BAA', 'NA' => 'ff5A1E82',
                                                     'NC' => 'ff5E3285', 'ND' => 'ff84471C', 'NE' => 'ffB82F89',
                                                     'NJ' => 'ffD2DB4A', 'NK' => 'ffF5A300', 'NM' => 'ffF5A300',
                                                     'NO' => 'ffB82F89', 'NP' => 'ff00B0A4', 'NS' => 'ff008CBE',
                                                     'NT' => 'ff00ACE7', 'O' => 'ffFF9BAA', 'S' => 'ff003399',
                                                     'T' => 'ffFF9BAA', 'TO' => 'ffE2001D', 'TT' => 'ffFD0000',
                                                     'V' => 'ffFF6600', 'W' => 'ff003399', 'X' => 'ff003399',
                                                     'Y' => 'ffFF9999', 'Z' => 'ffFF9999')),
          'LausanneJourney' => array('places' => array('Lausanne', 'Prilly', 'Lutry', 'Pully', 'Paudex',
                                                       'Crissier', 'Epalignes', 'Renens VD', 'St-Sulpice VD',
                                                       'Ecublens VD', 'Echichens'),
                                     'colors' => array('1' => 'ffeb1c20', '2' => 'fffcec00', '4' => 'ff00a34c',
                                                       '6' => 'ff00aeed', '7' => 'ff009c2f', '8' => 'ff8d52a1',
                                                       '9' => 'ffeb0273', '12' => 'ff8c9c7b', '13' => 'fffab514',
                                                       '16' => 'ffbdb004', '17' => 'ffacd48a', '18' => 'ff6ed1f5',
                                                       '21' => 'ff8b519e', '22' => 'ffc9562c', '25' => 'ffacc3e3',
                                                       '31' => 'ff519cb8', '32' => 'ff9c86bd', '33' => 'ff4b7320',
                                                       '36' => 'ff9db5bd', '38' => 'ffb88432',
                                                       '41' => 'ffb31e8b', '42' => 'ffffd903',
                                                       '45' => 'ff008d91', '46' => 'ffb39b04', '47' => 'fffccd7c',
                                                       '48' => 'ff7d9ed1', '49' => 'ffbf93c2', '54' => 'ffb86749',
                                                       '60' => 'ffcee3ac', '62' => 'ffe670a1',
                                                       '64' => 'ffb39b04', '65' => 'fffab514', '66' => 'fff28046')),
          'ZurichJourney' => array('places' => array('Zürich', 'Zürich Rehalp', 'Zürich Flughafen', 'Zürich Altstetten',
                                                     'Zürich Hegibachplatz', 'Schlieren', 'Zürich Tiefenbrunnen',
                                                     'Stettbach', 'Zürich Enge', 'Zürich Oerlikon'),
                                     'colors' => array('2' => 'ffEE1D23', '3' => 'ff00AB4D', '4' => 'ff48479D',
                                                       '5' => 'ff946237', '6' => 'ffD99E4E', '7' => 'ff231F20',
                                                       '8' => 'ffA6CE39', '9' => 'ff48479D', '10' => 'ffED3896',
                                                       '11' => 'ff00AB4D', '12' => 'ff78D0E2', '13' => 'ffFED304',
                                                       '14' => 'ff00AEEF', '15' => 'ffEE1D23', '17' => 'ffA1276F')));
    static $SHORT_CAT_EXCLUDES = array('T' => 'Tram', 'B' => 'Bus');
    static $SUB_CAT_EXCLUDES = array('S', 'U', 'M');

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string
     */
    public $shortCategory;

    /**
     * @var string
     */
    public $number;

    /**
     * @var string
     */
    public $operator;

    /**
     * @var string
     */
    public $to;

    /**
     * @var array
     */
    public $passList = array();

    /**
     * @var int
     */
    public $capacity1st = null;

    /**
     * @var int
     */
    public $capacity2nd = null;

    /**
     * @var string
     */
    public $color;

    /**
     * @var string
     */
    public $textColor;

    /**
     * @var string
     */
    public $resolvedNumber;

    /**
     * @var string, for technical use when we want to request the route
     */
    public $jHandle;


    static public function resolveColor(Journey $obj)
    {
        $dest_pieces = explode(',', $obj->to);
        $dest_piece = $dest_pieces[0];

        foreach (self::$JOURNEYS as $values) {
            if (in_array($dest_piece, $values['places'])) {
                if (isset($values['colors'][$obj->number])) {
                    return $values['colors'][$obj->number];
                }
            }
        }
        return null;
    }

    static public function resolveNumber(Journey $obj)
    {
        if (in_array($obj->shortCategory, array_keys(self::$SHORT_CAT_EXCLUDES)) && strlen($obj->number) <= 3) {
            $obj->category = self::$SHORT_CAT_EXCLUDES[$obj->shortCategory];
            return $obj->number;
        }
        // Handle S and U
        if (in_array($obj->shortCategory, self::$SUB_CAT_EXCLUDES) && strlen($obj->number) <= 2){
            if (strtoupper(substr($obj->number, 0, 1)) == $obj->shortCategory)
                return $obj->number;
            else
                return $obj->shortCategory.$obj->number;
        }
        if (!is_null($obj->color)) {
            return $obj->number;
        }
        return $obj->category;
    }

    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Provider $provider, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new Journey();
        }

        $obj->jHandle = implode(";",current($xml->JHandle->attributes()));

        if ($xml->JourneyAttributeList) {
            foreach ($xml->JourneyAttributeList->JourneyAttribute AS $journeyAttribute) {

                switch ($journeyAttribute->Attribute['type']) {
                    case 'NAME':
                        $obj->name = (string) $journeyAttribute->Attribute->AttributeVariant->Text;
                        break;
                    case 'CATEGORY':
                        $obj->category = (string) $journeyAttribute->Attribute->AttributeVariant->Text;
                        break;
                    case 'INTERNALCATEGORY':
                        $obj->subcategory = (string) $journeyAttribute->Attribute->AttributeVariant->Text;
                        break;
                    case 'NUMBER':
                        if (!$obj->number)
                            $obj->number = (string) $journeyAttribute->Attribute->AttributeVariant->Text;
                        break;
                    case 'LINE':
                        $obj->number = (string) $journeyAttribute->Attribute->AttributeVariant->Text;
                        break;
                    case 'OPERATOR':
                        $obj->operator = (string) $journeyAttribute->Attribute->AttributeVariant->Text;
                        break;
                    case 'DIRECTION':
                        $obj->to = (string) $journeyAttribute->Attribute->AttributeVariant->Text;
                        break;
                }
            }
        }

        $capacities1st = array();
        $capacities2nd = array();

        if ($xml->PassList->BasicStop) {
            foreach ($xml->PassList->BasicStop AS $basicStop) {
                if ($basicStop->Arr || $basicStop->Dep) {
                    $stop = Stop::createFromXml($basicStop, $date, null);
                    if ($stop->prognosis->capacity1st) {
                        $capacities1st[] = (int) $stop->prognosis->capacity1st;
                    }
                    if ($stop->prognosis->capacity2nd) {
                        $capacities2nd[] = (int) $stop->prognosis->capacity2nd;
                    }
                    $obj->passList[] = $stop;
                }
            }
        }

        if (count($capacities1st) > 0) {
            $obj->capacity1st = max($capacities1st);
        }
        if (count($capacities2nd) > 0) {
            $obj->capacity2nd = max($capacities2nd);
        }

        $obj->shortCategory = $provider::getShortCategory($obj->category);
        $obj->color = self::resolveColor($obj);
        $obj->resolvedNumber = self::resolveNumber($obj);

        return $obj;
    }

    public static function createFromStbXml(\SimpleXMLElement $xml, Provider $provider, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new Journey();
        }
        $capacity = (string) $xml['capacity'];
        if ($capacity and $capacity != '0|0') {
            $capacities = explode("|", $capacity, 2);
            $obj->capacity1st = $capacities[0];
            $obj->capacity2nd = $capacities[1];
        }


        $class = (int) $xml['class'];
        $obj->to = (string) $xml[$provider::$DEST_ATTR1];
        if (!$obj->to)
            $obj->to = (string) $xml[$provider::$DEST_ATTR2];
        $obj->number = (string)$xml['line'];

        // resolving name, number and category
        $hafasname = preg_replace('/\s+/', ' ', (string)$xml['hafasname']);
        $prod = preg_replace('/\s+/', ' ', (string)$xml['prod']);

        $prods = explode("#", $prod, 2);
        $obj->name = $prods[0];
        $obj->category = $prods[1];

        if (!$obj->number) {
            $catnumber = explode(" ", $obj->name, 2);
            if (sizeof($catnumber) > 1)
                $obj->number = $catnumber[1];
            else
                $obj->number = $obj->category;
        }

        // handle compound numbers
        $num_parts = explode(' ', $obj->number);
        if (sizeof($num_parts) > 1) {
            $obj->number = $num_parts[1];
        }

        // -- resolve shortCategory
        if ($class)
            $obj->shortCategory = $provider::intToProduct($class);
        else
            $obj->shortCategory = $provider::getShortCategory($obj->category);
        // -- end shortCategory


        // jhandle for route
        $jhandle = array($obj->name, $xml['dirnr']);
        $obj->jHandle = implode(";", $jhandle);

        //---resolve color
        $bg_color = (string) $xml['lineBG'];
        $fg_color = (string) $xml['lineFG'];
        if ($bg_color and $bg_color != 'ffffff')
            $obj->color = 'ff' . $bg_color;
        else
            $obj->color = self::resolveColor($obj);

        if ($fg_color and $fg_color != '000000')
            $obj->textColor = 'ff' . $fg_color;
        //---end color
        $obj->resolvedNumber = self::resolveNumber($obj);

        return $obj;
    }
}
