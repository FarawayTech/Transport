<?php

namespace Transport\Entity\Schedule;


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
                                                     'Confignon', 'Plan-les-Ouates', 'Satigny', 'Ferney'),
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
//    const CAT_REGEX = "/^(SN\\d{1,2}|S\\d{1,2})$/";
//    static $CAT_ARRAY = array('BUS', 'EXT', 'FUN', 'BAT', 'BAV', 'LB', 'R', 'IR', 'IC', 'ICN','ICE', 'RE', 'ECN', 'EC',
//                              'CNL','TGV', 'TLK');
    static $CAT_EXCLUDE = array('NFO', 'NFB', 'NFT', 'M', 'TRO', 'T');

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
    public $resolvedNumber;

    /**
     * @var string, for technical use when we want to request the route
     */
    public $jHandle;


    static public function resolveColor(Journey $obj)
    {
        $dest_pieces = explode(',', $obj->to);
        $dest_piece = $dest_pieces[0];

        foreach (self::$JOURNEYS as $name => $values) {
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
        $resolvedNumber = $obj->number;
        if (is_null($obj->color) && !in_array($obj->category, self::$CAT_EXCLUDE))
        {
            $resolvedNumber = $obj->category;
        }
        return $resolvedNumber;
    }

    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new Journey();
        }

        $obj->jHandle = implode(";",current($xml->JHandle->attributes()));

        // TODO: get attributes
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

        $obj->color = self::resolveColor($obj);
        $obj->resolvedNumber = self::resolveNumber($obj);

        return $obj;
    }
}
