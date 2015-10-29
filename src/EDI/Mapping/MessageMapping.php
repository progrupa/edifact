<?php

namespace EDI\Mapping;


class MessageMapping
{
    /** @var  array */
    private $defaults = array();
    /** @var  MessageSegmentMapping[] */
    private $segments = array();

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
     * @return MessageSegmentMapping[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param MessageSegmentMapping[] $segments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
    }

    /**
     * @param MessageSegmentMapping $segment
     */
    public function addSegment(MessageSegmentMapping $segment)
    {
        $this->segments[] = $segment;
    }
}
