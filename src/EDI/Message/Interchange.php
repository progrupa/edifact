<?php

namespace EDI\Message;


use EDI\Annotations\Mandatory;
use EDI\Annotations\SegmentPiece;

class Interchange
{
    /**
     * @SegmentPiece(position="1", parts={"name", "version", "serviceCodeList", "encoding"})
     * @Mandatory
     */
    private $syntax;
    private $sender;
    private $recipient;
    private $time;
    private $controlReference;
    private $recipientsReference;
    private $applicationReference;
    private $processingPriorityCode;
    private $acknowledgementRequest;
    private $agreementIdentifier;
    private $testIndicator;
    /** @var  Message[] */
    private $messages;

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
}
