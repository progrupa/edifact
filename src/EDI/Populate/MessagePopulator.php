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
        $message = new Message();
        $this->fillProperties($message, $data);

        $identifier = $message->getIdentifier();
        $mapping = $this->mappingLoader->loadMessage($identifier['version'], $identifier['release'], $identifier['type']);
        $this->segmentPopulator->setSegmentConfig($this->mappingLoader->loadSegments($identifier['version'], $identifier['release']));

        $expectedSegments = $mapping->getSegments();
        $segment = $this->getNextSegment($data);
        $this->nextExpectedSegment($expectedSegments);  //  Ignore the header, already processed when creating Message
        $expected = $this->nextExpectedSegment($expectedSegments);

        while ('UNT' != $segment->getCode()) {
            /** @var MessageSegmentMapping $expected */
            if (! $expected) {
                print "Run out of expectations\n";
                break;
            }
//            print sprintf("Expected %s, processing %s\n", $expected->getCode(), $segment->getCode());
            if ($expected->acceptSegment($segment)) {
//                print "Segment added!\n";
//                $message->addSegment($segment);
                $segment = $this->getNextSegment($data);
            } else {
                if ($expected->isRequired()) {
//                    throw new IncorrectSegmentId(sprintf('Expected segment %s, got %s instead', $expected->getCode(), $segment->getCode()));
                }
                $message->addSegments($expected->getSegments());
                $expected = $this->nextExpectedSegment($expectedSegments);
            }
        }

        $trailer = new MessageTrailer();
        $this->fillProperties($trailer, $segment);
        $message->setTrailer($trailer);

        return array($message);
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

    private function isExpectedSegment(MessageSegmentMapping $expected, Segment $segment)
    {
        return $expected->expectedCode() == $segment->getCode();
    }
}
