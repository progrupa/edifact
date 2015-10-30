<?php

namespace EDI\Message;


use EDI\Annotations;

/**
 * @Annotations\Segment("UNB")
 */
class Interchange
{
    /**
     * @Annotations\SegmentPiece(position="1", parts={"name":{"@mandatory"}, "version":{"@mandatory"}, "serviceCodeList", "encoding"})
     * @Annotations\Mandatory
     */
    private $syntax;
    /**
     * @Annotations\SegmentPiece(position="2", parts={"id":{"@mandatory"}, "codeQualifier", "internalId", "internalSubId"})
     * @Annotations\Mandatory()
     */
    private $sender;
    /**
     * @Annotations\SegmentPiece(position="3", parts={"id":{"@mandatory"}, "codeQualifier", "internalId", "internalSubId"})
     * @Annotations\Mandatory()
     */
    private $recipient;
    /**
     * @Annotations\SegmentPiece(position="4", parts={"date":{"@mandatory"}, "time":{"@mandatory"}})
     * @Annotations\Mandatory()
     */
    private $time;
    /**
     * @Annotations\SegmentPiece(position="5")
     */
    private $controlReference;
    /**
     * @Annotations\SegmentPiece(position="6", parts={"password":{"@mandatory"}, "passwordQualifier"})
     */
    private $recipientsReference;
    /**
     * @Annotations\SegmentPiece(position="7")
     */
    private $applicationReference;
    /**
     * @Annotations\SegmentPiece(position="8")
     */
    private $processingPriorityCode;
    /**
     * @Annotations\SegmentPiece(position="9")
     */
    private $acknowledgementRequest;
    /**
     * @Annotations\SegmentPiece(position="10")
     */
    private $agreementIdentifier;
    /**
     * @Annotations\SegmentPiece(position="11")
     */
    private $testIndicator;

    /** @var  Message[] */
    private $messages;
    /** @var  InterchangeTrailer */
    private $trailer;

    /**
     * @return mixed
     */
    public function getSyntax()
    {
        return $this->syntax;
    }

    /**
     * @param mixed $syntax
     */
    public function setSyntax($syntax)
    {
        $this->syntax = $syntax;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param mixed $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
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

    /**
     * @return mixed
     */
    public function getRecipientsReference()
    {
        return $this->recipientsReference;
    }

    /**
     * @param mixed $recipientsReference
     */
    public function setRecipientsReference($recipientsReference)
    {
        $this->recipientsReference = $recipientsReference;
    }

    /**
     * @return mixed
     */
    public function getApplicationReference()
    {
        return $this->applicationReference;
    }

    /**
     * @param mixed $applicationReference
     */
    public function setApplicationReference($applicationReference)
    {
        $this->applicationReference = $applicationReference;
    }

    /**
     * @return mixed
     */
    public function getProcessingPriorityCode()
    {
        return $this->processingPriorityCode;
    }

    /**
     * @param mixed $processingPriorityCode
     */
    public function setProcessingPriorityCode($processingPriorityCode)
    {
        $this->processingPriorityCode = $processingPriorityCode;
    }

    /**
     * @return mixed
     */
    public function getAcknowledgementRequest()
    {
        return $this->acknowledgementRequest;
    }

    /**
     * @param mixed $acknowledgementRequest
     */
    public function setAcknowledgementRequest($acknowledgementRequest)
    {
        $this->acknowledgementRequest = $acknowledgementRequest;
    }

    /**
     * @return mixed
     */
    public function getAgreementIdentifier()
    {
        return $this->agreementIdentifier;
    }

    /**
     * @param mixed $agreementIdentifier
     */
    public function setAgreementIdentifier($agreementIdentifier)
    {
        $this->agreementIdentifier = $agreementIdentifier;
    }

    /**
     * @return mixed
     */
    public function getTestIndicator()
    {
        return $this->testIndicator;
    }

    /**
     * @param mixed $testIndicator
     */
    public function setTestIndicator($testIndicator)
    {
        $this->testIndicator = $testIndicator;
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param Message[] $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return InterchangeTrailer
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    /**
     * @param InterchangeTrailer $trailer
     */
    public function setTrailer($trailer)
    {
        $this->trailer = $trailer;
    }

    /**
     * @return InterchangeTrailer
     */
    public function createTrailer()
    {
        $trailer = new InterchangeTrailer();
        $trailer->setControlReference($this->controlReference);
        $trailer->setControlCount(count($this->messages));

        return $trailer;
    }
}
