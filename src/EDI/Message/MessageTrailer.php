<?php

namespace EDI\Message;


use EDI\Annotations;

/**
 * @Annotations\Segment("UNT")
 */
class MessageTrailer
{
    /**
     * @Annotations\SegmentPiece(position="1")
     * @Annotations\Mandatory
     */
    private $segmentCount;
    /**
     * @Annotations\SegmentPiece(position="2")
     * @Annotations\Mandatory
     */
    private $referenceNumber;

    /**
     * @return mixed
     */
    public function getSegmentCount()
    {
        return $this->segmentCount;
    }

    /**
     * @param mixed $segmentCount
     */
    public function setSegmentCount($segmentCount)
    {
        $this->segmentCount = $segmentCount;
    }

    /**
     * @return mixed
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * @param mixed $referenceNumber
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
    }
}
