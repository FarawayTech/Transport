<?php

namespace Transport\Entity\Schedule;

class FribourgJourney
{
    public static $PLACES = array('Fribourg', 'Marly', 'Villars-sur-Glâne', 'Granges-Paccot', 'Givisiez');
    public static $COLORS = array('1' => 'ff53B401', '2' => 'ffD83B38', '3' => 'ffFFD012',
                                  '4' => 'ffF08B32', '5' => 'ff2F7CE3', '6' => 'ff3F2072',
                                  '7' => 'ff833816');
}

class BielJourney
{
    public static $PLACES = array('Biel/Bienne', 'Nidau', 'Brügg BE', 'Tüscherz');
    public static $COLORS = array('1' => 'ffd92a31', '2' => 'ff293e80', '4' => 'ffe4822e',
                                  '5' => 'ff116630', '6' => 'ff9fc841', '7' => 'ffdf4891',
                                  '8' => 'ff7c4391', '11' => 'ff9f2e36');
}

class BaselJourney
{
    public static $PLACES = array('Basel', 'Pratteln', 'Rodersdorf', 'Dornach-Arlesheim', 'Ettingen',
    'Flüh', 'Basel Wiesenplatz', 'Birsfelden', 'Allschwil', 'Riehen', 'Hüslimatt', 'Aesch', 'St-Louis Grenze',
    'Basel Messeplatz');
    public static $COLORS = array('1' => 'ff804b2f', '2' => 'ffa8834a', '3' => 'ff3c4796', '6' => 'ff006ab0',
                                  '8' => 'ffeb6ea2', '10' => 'ffffca0a','11' => 'ffe30910', '14' => 'ffeb7b05',
                                  '15' => 'ff00963a', '16' => 'ffaecc06', '17' => 'ff00a3e3', '21' => 'ff00a191');
}

class Journey
{
    const CAT_REGEX = "/^(BUS|FUN|BAT|BAV|R|IR|IC|ICN|ICE|RE|ECN|S\\d{1,2})$/";

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

    // TODO: how to optimize it?
    static public function resolveColor(Journey $obj)
    {
        $dest_pieces = explode(',', $obj->to);
        $dest_piece = $dest_pieces[0];
        if (in_array($dest_piece, FribourgJourney::$PLACES)) {
            if (isset(FribourgJourney::$COLORS[$obj->number])) {
                return FribourgJourney::$COLORS[$obj->number];
            }
        }
        else if (in_array($dest_piece, BielJourney::$PLACES)) {
            if (isset(BielJourney::$COLORS[$obj->number])) {
                return BielJourney::$COLORS[$obj->number];
            }
        }
        else if (in_array($dest_piece, BaselJourney::$PLACES)) {
            if (isset(BaselJourney::$COLORS[$obj->number])) {
                return BaselJourney::$COLORS[$obj->number];
            }
        }
        return null;
    }

    static public function resolveNumber(Journey $obj)
    {
        $resolvedNumber = $obj->number;
        if (is_null($obj->color) && preg_match(self::CAT_REGEX, $obj->category))
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
