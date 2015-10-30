<?php

namespace EDI\Populate;


use Doctrine\Common\Annotations\Reader;
use EDI\Message\Interchange;
use EDI\Message\InterchangeTrailer;

class InterchangePopulator extends Populator
{
    /** @var  MessagePopulator */
    private $messagePopulator;

    public function __construct(Reader $annotationReader, $messagePopulator)
    {
        parent::__construct($annotationReader);
        $this->messagePopulator = $messagePopulator;
    }


    /**
     * @param array $data
     * @return Interchange
     * @throws \EDI\Exception\MandatorySegmentPieceMissing
     */
    public function populate(&$data)
    {
        $interchange = new Interchange();
        $this->fillProperties($interchange, $data);

        $interchange->setMessages($this->messagePopulator->populate($data));

        $trailer = new InterchangeTrailer();
        $this->fillProperties($trailer, $data);
        $interchange->setTrailer($trailer);

        return $interchange;
    }
}
