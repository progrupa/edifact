<?php

namespace EDI\Message;


use EDI\Annotations\Mandatory;
use EDI\Annotations\Segment;
use EDI\Annotations\SegmentPiece;

#[Segment("UNT")]
class MessageTrailer
{
    #[SegmentPiece(position: 1)]
    #[Mandatory]
    private $segmentCount;

    #[SegmentPiece(position: 2)]
    #[Mandatory]
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
