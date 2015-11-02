<?php
/**
 * Created by PhpStorm.
 * User: dominikkasprzak
 * Date: 23/10/15
 * Time: 10:02
 */

namespace EDI\Populate;


use Doctrine\Common\Annotations\Reader;
use EDI\Exception\IncorrectSegmentId;
use EDI\Mapping\MappingLoader;
use EDI\Mapping\MessageSegmentMapping;
use EDI\Message\Message;
use EDI\Message\MessageTrailer;
use EDI\Message\Segment;

class MessagePopulator extends Populator
{
    /** @var  SegmentPopulator */
    private $segmentPopulator;
    /** @var  MappingLoader */
    private $mappingLoader;

    public function __construct(Reader $annotationReader, SegmentPopulator $segmentPopulator, MappingLoader $mappingLoader)
    {
        parent::__construct($annotationReader);
        $this->segmentPopulator = $segmentPopulator;
        $this->mappingLoader = $mappingLoader;
    }

    /**
     * @param array $data
     * @return Message[]
     * @throws \EDI\Exception\MandatorySegmentPieceMissing
     */
    public function populate(&$data)
    {
        $messages = [];
        $remainingData = null;
        $segment = $this->getNextSegment($data);

        while ($segment && $segment->getCode() == 'UNH') {
            $message = new Message();
            $this->fillProperties($message, $segment);

            $identifier = $message->getIdentifier();
            $mapping = $this->mappingLoader->loadMessage($identifier['version'], $identifier['release'],
                $identifier['type']);
            $this->segmentPopulator->setSegmentConfig($this->mappingLoader->loadSegments($identifier['version'],
                $identifier['release']));

            $expectedSegments = $mapping->getSegments();
            $segment = $this->getNextSegment($data);
            $this->nextExpectedSegment($expectedSegments);  //  Ignore the header, already processed when creating Message
            $expected = $this->nextExpectedSegment($expectedSegments);

            while ('UNT' != $segment->getCode()) {
                /** @var MessageSegmentMapping $expected */
                if (!$expected) {
                    break;
                }

                if ($expected->acceptSegment($segment)) {
                    $segment = $this->getNextSegment($data);
                } else {
                    $message->addSegments($expected->getSegments());
                    $expected = $this->nextExpectedSegment($expectedSegments);
                }
            }

            $trailer = new MessageTrailer();
            $this->fillProperties($trailer, $segment);
            $message->setTrailer($trailer);

            $messages[] = $message;

            $remainingData = reset($data);
            $segment = $this->getNextSegment($data);
        }

        if ($remainingData) {
            array_unshift($data, $remainingData);
        }
        return $messages;
    }

    /**
     * @return Segment
     * @throws \EDI\Exception\UnknownSegmentException
     */
    protected function getNextSegment(&$data)
    {
        return $this->segmentPopulator->populate($data);
    }

    /**
     * @return mixed
     */
    protected function nextExpectedSegment(&$expectedSegments)
    {
        return array_shift($expectedSegments);
    }
}
