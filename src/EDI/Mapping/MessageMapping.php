<?php

namespace EDI\Mapping;


class MessageMapping
{
    /** @var  array */
    private $defaults;
    /** @var  array */
    private $segments;

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param array $segments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
    }
}
