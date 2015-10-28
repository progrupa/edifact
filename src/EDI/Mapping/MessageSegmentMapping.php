<?php

namespace EDI\Mapping;


use EDI\Message\Segment;

class MessageSegmentMapping
{
    /** @var string */
    private $code;
    /** @var int */
    private $maxRepeat;
    /** @var bool  */
    private $required = false;
    /** @var  Segment[] */
    protected $segments = array();

    public function __construct($code, $maxRepeat, $required = false)
    {
        $this->code = $code;
        $this->maxRepeat = $maxRepeat;
        $this->required = $required;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getMaxRepeat()
    {
        return $this->maxRepeat;
    }

    /**
     * @param int $maxRepeat
     */
    public function setMaxRepeat($maxRepeat)
    {
        $this->maxRepeat = $maxRepeat;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function expectedCode()
    {
        return $this->code;
    }

    public function acceptSegment(Segment $segment)
    {
        if ($this->getCode() == $segment->getCode() && $this->maxRepeat > count($this->segments)) {
            $this->segments[] = $segment;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \EDI\Message\Segment[]
     */
    public function getSegments()
    {
        return $this->segments;
    }
}
