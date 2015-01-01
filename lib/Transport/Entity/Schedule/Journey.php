<?php

namespace Transport\Entity\Schedule;


use Transport\Providers\Provider;

class Journey
{
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
    private $lines;


    private function resolveColor()
    {
        return @$this->lines[$this->resolvedNumber];
    }

    private function resolveNumber()
    {
        if (in_array($this->shortCategory, array_keys(self::$SHORT_CAT_EXCLUDES)) && strlen($this->number) <= 3) {
            $this->category = self::$SHORT_CAT_EXCLUDES[$this->shortCategory];
            return $this->number;
        }
        // Handle S and U
        if (in_array($this->shortCategory, self::$SUB_CAT_EXCLUDES) && strlen($this->number) <= 2){
            if (strtoupper(substr($this->number, 0, 1)) == $this->shortCategory)
                return $this->number;
            else
                return $this->shortCategory.$this->number;
        }
        return $this->category;
    }

    static public function createFromXml(\SimpleXMLElement $xml, \DateTime $date, $lines, Provider $provider, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new Journey();
        }
        $obj->lines = $lines;

        $obj->jHandle = implode(";",current($xml->JHandle->attributes()));

        if ($xml->JourneyAttributeList) {
            foreach ($xml->JourneyAttributeList->JourneyAttribute AS $journeyAttribute) {

                switch ($journeyAttribute->Attribute['type']) {
                    case 'NAME':
                        $obj->name = trim((string) $journeyAttribute->Attribute->AttributeVariant->Text);
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
        $obj->resolvedNumber = $obj->resolveNumber();
        $obj->color = $obj->resolveColor();

        return $obj;
    }

    public static function createFromStbXml(\SimpleXMLElement $xml, $lines, Provider $provider, Journey $obj = null)
    {
        if (!$obj) {
            $obj = new Journey();
        }
        $obj->lines = $lines;
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

        $obj->resolvedNumber = $obj->resolveNumber();

        //---resolve color
        $bg_color = (string) $xml['lineBG'];
        $fg_color = (string) $xml['lineFG'];
        if ($bg_color and $bg_color != 'ffffff')
            $obj->color = 'ff' . $bg_color;
        else
            $obj->color = $obj->resolveColor();

        if ($fg_color and $fg_color != '000000')
            $obj->textColor = 'ff' . $fg_color;
        //---end color

        return $obj;
    }
}
