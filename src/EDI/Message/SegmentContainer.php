<?php

namespace EDI\Message;


trait SegmentContainer
{
    /** @var  Segment[] */
    private $segments = [];

    /**
     * @return Segment[]
     */
    public function getSegments($code = null)
    {
        if ($code) {
            $segments = array();
            foreach ($this->segments as $segment) {
                if ($segment->getCode() == $code) {
                    $segments[] = $segment;
                }
            }
            return $segments;
        }
        return $this->segments;
    }

    /**
     * @param Segment[] $segments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
    }

    public function addSegment(Segment $segment)
    {
        $this->segments[] = $segment;
        return $this;
    }
}
