<?php

namespace EDI\Mapping;


use EDI\Exception\RequiredSegmentMissing;
use EDI\Message\Segment;
use EDI\Message\SegmentGroup;

class MessageSegmentGroupMapping extends MessageSegmentMapping
{

    /** @var  MessageSegmentMapping[] */
    private $segmentMappings;
    /** @var MessageSegmentMapping[] */
    private $currentGroup = array();
    /** @var int  */
    private $lastProcessed = -1;
    /** @var  MessageSegmentMapping */
    private $currentSegment;

    /**
     * @return MessageSegmentMapping[]
     */
    public function getSegmentMappings()
    {
        return $this->segmentMappings;
    }

    /**
     * @param MessageSegmentMapping[] $segmentMappings
     */
    public function setSegmentMappings($segmentMappings)
    {
        $this->segmentMappings = $segmentMappings;
    }

    /**
     * @param MessageSegmentMapping $segments
     */
    public function addSegment(MessageSegmentMapping $segment)
    {
        $this->segmentMappings[] = $segment;
    }

    public function expectedCode()
    {
        return $this->segmentMappings[0]->expectedCode();
    }

    public function hasSegments()
    {
        return parent::hasSegments() || !empty($this->currentGroup) || !is_null($this->currentSegment);
    }

    public function getSegments()
    {
        if ($this->currentSegment) {
            $this->currentGroup[] = $this->currentSegment;
        }

        $currentGroupSegments = $this->getCurrentGroupSegments();
        if (! empty($currentGroupSegments)) {
            $this->checkCurrentGroup();
            $group = new SegmentGroup($this->getCode());
            $group->setSegments($currentGroupSegments);
            $this->segments[] = $group;
        }
        return parent::getSegments();
    }

    public function acceptSegment(Segment $segment)
    {
        //  current segment mapping is accepting more segments
        if ($this->currentSegment) {
            if ($this->currentSegment->acceptSegment($segment)) {
                return true;
            } else {
                $this->currentGroup[] = $this->currentSegment;
            }
        }

        //  continue open group
        for ($i = ($this->lastProcessed + 1); $i < count($this->segmentMappings); $i++) {
            $this->currentSegment = clone($this->segmentMappings[$i]);
            if ($this->currentSegment->acceptSegment($segment)) {
                $this->lastProcessed = $i;
                return true;
            }
            $this->currentGroup[] = $this->currentSegment;
        }
        //  close current segment, try opening next copy

        $currentGroupSegments = $this->getCurrentGroupSegments();
        if (! empty($currentGroupSegments)) {
            $this->checkCurrentGroup();
            $group = new SegmentGroup($this->getCode());
            $group->setSegments($currentGroupSegments);
            $this->segments[] = $group;
        }

        $this->currentGroup = array();
        $this->currentSegment = null;
        $this->lastProcessed = -1;

        for ($i = 0; $i < count($this->segmentMappings); $i++) {
            $this->currentSegment = clone($this->segmentMappings[$i]);
            if ($this->currentSegment->acceptSegment($segment)) {
                $this->lastProcessed = $i;
                return true;
            }
            $this->currentGroup[] = $this->currentSegment;
        }

        return false;
    }

    private function checkCurrentGroup()
    {
        foreach ($this->currentGroup as $segmentMapping) {
            if ($segmentMapping->isRequired() && !count($segmentMapping->getSegments())) {
                throw new RequiredSegmentMissing(sprintf("Required segment %s missing in group %s", $segmentMapping->getCode(), $this->getCode()));
            }
        }
    }

    private function getCurrentGroupSegments()
    {
        $segments = array();
        foreach ($this->currentGroup as $segmentMapping) {
            $segments = array_merge($segments, $segmentMapping->getSegments());
        }
        return $segments;
    }
}
