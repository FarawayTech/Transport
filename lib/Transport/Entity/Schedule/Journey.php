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
                                                       'Givisiez', 'Rosé', 'Chésopelloz'),
                                     'colors' => array('1' => 'ff53B401', '2' => 'ffD83B38', '3' => 'ffFFD012',
                                                       '4' => 'ffF08B32', '5' => 'ff2F7CE3', '6' => 'ff3F2072',
                                                       '7' => 'ff833816')),
          'GenevaJourney' => array('places' => array('Genève', 'Carouge GE', 'Veyrier', 'Bernex', 'Lancy',
                                                     'Meyrin', 'Chêne-Bourg', 'Thônex', 'Grand-Saconnex', 'Vernier',
                                                     'Onex', 'Veigy', 'Neydens', 'Grand-Lancy'),
                                   'colors' => array('1' => 'ff393185', '2' => 'ffd2d64d', '3' => 'ffa34d90',
                                                     '4' => 'ffd10026', '5' => 'ff5ec5eb', '6' => 'ff0098d4',
                                                     '7' => 'ff009c2f', '8' => 'ff804826', '9' => 'ffd10026',
                                                     '10' => 'ff00693a', '11' => 'ff8278b0', '12' => 'fff5a300',
                                                     '14' => 'ff393185', '15' => 'ff8c4f29', '18' => 'ffa34d90',
                                                     '19' => 'fffcd700', '21' => 'ff70163f', '22' => 'ff393185',
                                                     '23' => 'ffa34d90', '25' => 'ff9e6229', '27' => 'ff5ec5eb',
                                                     '28' => 'fffcd700')),
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
    static $CAT_EXCLUDE = array('NFO', 'NFB', 'M');

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
