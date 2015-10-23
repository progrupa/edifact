<?php

namespace EDI\Message;


use EDI\Annotations;

/**
 * @Annotations\Segment("UNZ")
 */
class InterchangeTrailer
{
    /**
     * @Annotations\SegmentPiece(position="1")
     * @Annotations\Mandatory
     */
    private $controlCount;
    /**
     * @Annotations\SegmentPiece(position="2")
     * @Annotations\Mandatory
     */
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
