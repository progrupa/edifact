<?php

namespace EDI\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Encoder;
use EDI\Mapping\DataElementMapping;
use EDI\Mapping\DataElementType;
use EDI\Mapping\MessageMapping;
use EDI\Mapping\MessageSegmentMapping;
use EDI\Mapping\SegmentMapping;
use EDI\Message\Interchange;
use EDI\Message\Message;
use EDI\Message\Segment;
use EDI\Printer\AnnotationPrinter;
use EDI\Printer\SegmentPrinter;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_convert_segment_to_string()
    {
        $segment = new Segment('XXX');
        $segment->var = 'value';
        $segmentMapping = new SegmentMapping('XXX');
        $segmentMapping->addDataElement(1, new DataElementMapping(2345, true, DataElementType::A, 'var'));

        $message = new Message();
        $message->setReferenceNumber(34834);
        $message->setIdentifier(["type" => 'TEST', "version" => 'S', "release" => '404', "controllingAgency" => 'PG']);
        $message->addSegment($segment);
//        $messageMapping = new MessageMapping();
//        $messageMapping->setDefaults(["0065" => 'TEST', "0052" => 'S', "0054" => '404', "0051" => 'PG']);
//        $messageMapping->addSegment(new MessageSegmentMapping('UNH', 1, true));
//        $messageMapping->addSegment(new MessageSegmentMapping('XXX', 99, true));
//        $messageMapping->addSegment(new MessageSegmentMapping('UNT', 1, true));

        $interchange = new Interchange();
        $interchange->setSyntax(["name" => 'UNOC', "version" => 3]);
        $interchange->setSender(['id' => 'zenon']);
        $interchange->setRecipient(['id' => 'stefan']);
        $interchange->setTime(['date' => '20150101', 'time' => '1034']);
        $interchange->setControlReference('17');
        $interchange->setMessages([$message]);

        $encoder = new Encoder(
            new AnnotationPrinter(new AnnotationReader()),
            new SegmentPrinter(['XXX' => $segmentMapping])
            );

        $result = $encoder->encode($interchange);

        $this->assertEquals("UNB+UNOC:3+zenon+stefan+20150101:1034+17'UNH+34834+TEST:S:404:PG'XXX+value'UNT+1+34834'UNZ+1+17'", $result);
    }
}
