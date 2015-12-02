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
     * @param $code
     * @return Segment
     */
    public function getNext($code)
    {
        do {
            $next = current($this->segments);
            next($this->segments);
        } while ($next !== false && $next->getCode() != $code);
        return $next;
    }

    /**
     * @param Segment[] $segments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
        reset($this->segments);
    }

    public function addSegment(Segment $segment)
    {
        $this->segments[] = $segment;
        reset($this->segments);
        return $this;
    }
}
