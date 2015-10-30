<?php

namespace EDI\Message;


use EDI\Annotations;

/**
 * @Annotations\Segment("UNH")
 */
class Message
{
    /**
     * @Annotations\SegmentPiece(position="1")
     * @Annotations\Mandatory()
     */
    private $referenceNumber;
    /**
     * @Annotations\SegmentPiece(position="2", parts={"type":{"@mandatory"}, "version":{"@mandatory"}, "release":{"@mandatory"}, "controllingAgency":{"@mandatory"}, "associationCode", "coeListVersion", "subfunction"})
     * @Annotations\Mandatory
     */
    private $identifier;
    private $commonAccessReference;
    private $transferStatus;
    private $subset;
    private $implementationGuideline;
    private $scenario;
    /** @var  MessageTrailer */
    private $trailer;

    use SegmentContainer;

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

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getCommonAccessReference()
    {
        return $this->commonAccessReference;
    }

    /**
     * @param mixed $commonAccessReference
     */
    public function setCommonAccessReference($commonAccessReference)
    {
        $this->commonAccessReference = $commonAccessReference;
    }

    /**
     * @return mixed
     */
    public function getTransferStatus()
    {
        return $this->transferStatus;
    }

    /**
     * @param mixed $transferStatus
     */
    public function setTransferStatus($transferStatus)
    {
        $this->transferStatus = $transferStatus;
    }

    /**
     * @return mixed
     */
    public function getSubset()
    {
        return $this->subset;
    }

    /**
     * @param mixed $subset
     */
    public function setSubset($subset)
    {
        $this->subset = $subset;
    }

    /**
     * @return mixed
     */
    public function getImplementationGuideline()
    {
        return $this->implementationGuideline;
    }

    /**
     * @param mixed $implementationGuideline
     */
    public function setImplementationGuideline($implementationGuideline)
    {
        $this->implementationGuideline = $implementationGuideline;
    }

    /**
     * @return mixed
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * @param mixed $scenario
     */
    public function setScenario($scenario)
    {
        $this->scenario = $scenario;
    }

    /**
     * @return MessageTrailer
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    public function createTrailer()
    {
        $trailer = new MessageTrailer();
        $trailer->setReferenceNumber($this->referenceNumber);
        $trailer->setSegmentCount($this->countSegments());

        return $trailer;
    }

    /**
     * @param MessageTrailer $trailer
     */
    public function setTrailer($trailer)
    {
        $this->trailer = $trailer;
    }

    public function addSegments($segments)
    {
        $this->segments = array_merge($this->segments, $segments);
    }

    private function countSegments()
    {
        $total = 0;
        foreach ($this->getSegments() as $seg) {
            $total += $seg->count();
        }

        return $total;
    }
}
