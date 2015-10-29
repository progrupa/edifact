<?php

namespace EDI\Printer;


abstract class Printer
{
    const FIELD_SEPARATOR = '+';
    const ELEMENT_SEPARATOR = ':';
    const NUMBER_SEPARATOR = '.';
    const ESCAPE_CHARACTER = '?';

    private $fieldSeparator = self::FIELD_SEPARATOR;
    private $elementSeparator = self::ELEMENT_SEPARATOR;
    private $numberSeparator = self::NUMBER_SEPARATOR;
    private $escapeCharacter = self::ESCAPE_CHARACTER;

    public function prepareString($object)
    {
        $properties = $this->getProperties($object);

        foreach ($properties as $k => $prop) {
            if (is_array($prop)) {
                $properties[$k] = implode($this->elementSeparator, $prop);
            }
        }

        return implode($this->fieldSeparator, $properties)."'";
    }

    abstract protected function getProperties($object);

    /**
     * @return string
     */
    public function getFieldSeparator()
    {
        return $this->fieldSeparator;
    }

    /**
     * @param string $fieldSeparator
     */
    public function setFieldSeparator($fieldSeparator)
    {
        $this->fieldSeparator = $fieldSeparator;
    }

    /**
     * @return string
     */
    public function getElementSeparator()
    {
        return $this->elementSeparator;
    }

    /**
     * @param string $elementSeparator
     */
    public function setElementSeparator($elementSeparator)
    {
        $this->elementSeparator = $elementSeparator;
    }

    /**
     * @return string
     */
    public function getNumberSeparator()
    {
        return $this->numberSeparator;
    }

    /**
     * @param string $numberSeparator
     */
    public function setNumberSeparator($numberSeparator)
    {
        $this->numberSeparator = $numberSeparator;
    }

    /**
     * @return string
     */
    public function getEscapeCharacter()
    {
        return $this->escapeCharacter;
    }

    /**
     * @param string $escapeCharacter
     */
    public function setEscapeCharacter($escapeCharacter)
    {
        $this->escapeCharacter = $escapeCharacter;
    }

    protected function weedOutEmpty($properties)
    {
        $lastValueIndex = -1;
        $lastIndex = max(array_keys($properties));
        $values = [];
        for ($i = 0; $i <= $lastIndex; $i++) {
            if (isset($properties[$i]) && !empty($properties[$i])) {
                $lastValueIndex = $i;
                $values[] = $properties[$i];
            } else {
                $values[] = null;
            }
        }

        return array_slice($values, 0, $lastValueIndex + 1);
    }
}
