<?php

namespace EDI\Message;


use EDI\Annotations\Mandatory;
use EDI\Annotations\Segment;
use EDI\Annotations\SegmentPiece;

#[Segment("UNZ")]
class InterchangeTrailer
{
    #[SegmentPiece(position: 1)]
    #[Mandatory]
    private $controlCount;

    #[SegmentPiece(position: 2)]
    #[Mandatory]
    private $controlReference;

    /**
     * @return mixed
     */
    public function getControlCount()
    {
        return $this->controlCount;
    }

    /**
     * @param mixed $controlCount
     */
    public function setControlCount($controlCount)
    {
        $this->controlCount = $controlCount;
    }

    /**
     * @return mixed
     */
    public function getControlReference()
    {
        return $this->controlReference;
    }

    /**
     * @param mixed $controlReference
     */
    public function setControlReference($controlReference)
    {
        $this->controlReference = $controlReference;
    }
}
