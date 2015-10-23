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
    /** @var  Segment[] */
    private $segments;
    /** @var  MessageTrailer */
    private $trailer;

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
     * @return Segment[]
     */
    public function getSegments()
    {
        return $this->segments;
    }

    /**
     * @param Segment[] $segments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;
    }

    /**
     * @return MessageTrailer
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    /**
     * @param MessageTrailer $trailer
     */
    public function setTrailer($trailer)
    {
        $this->trailer = $trailer;
    }
}
