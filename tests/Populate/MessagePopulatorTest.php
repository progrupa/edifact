<?php
/**
 * Created by PhpStorm.
 * User: dominikkasprzak
 * Date: 23/10/15
 * Time: 12:15
 */

namespace EDI\Tests\Populate;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Mapping\MappingLoader;
use EDI\Mapping\MessageMapping;
use EDI\Message\Message;
use EDI\Message\Segment;
use EDI\Parser;
use EDI\Populate\MessagePopulator;
use EDI\Populate\SegmentPopulator;

class MessagePopulatorTest extends \PHPUnit_Framework_TestCase
{
//    /** @test */
//    public function should_load_proper_segment_config()
//    {
//        /** @var MappingLoader $loader */
//        $loader = \Phake::mock(MappingLoader::class);
//        \Phake::when($loader)->loadMessage(\Phake::anyParameters())->thenReturn(new MessageMapping());
//
//        $segmentPopulator = $this->givenSegmentPopulator();
//
//        $populator = $this->givenPopulator($segmentPopulator, $loader, '/some/path');
//
//        $fixtureDir = realpath(__DIR__ . '/../fixtures');
//        $parser = new Parser();
//
//        $data = $parser->parse($fixtureDir . '/invoic_message_standalone.edi');
//        $populator->populate($data);
//
//        \Phake::verify($loader)->loadMessage('/some/path/D96A/messages/invoic.xml');
//    }

    /** @test */
    public function should_use_segment_populator_for_content()
    {
        $loader = new MappingLoader();
        $mappingDir = realpath(__DIR__ . '/../../src/EDI/Mapping');

        $segmentPopulator = $this->givenSegmentPopulator();

        $populator = $this->givenPopulator($segmentPopulator, $loader, $mappingDir);

        $fixtureDir = realpath(__DIR__ . '/../fixtures');
        $parser = new Parser();

        $data = $parser->parse($fixtureDir . '/invoic_message_standalone.edi');
        $populator->populate($data);

        \Phake::verify($segmentPopulator, \Phake::times(35))->populate(\Phake::anyParameters());
    }

    /** @test */
    public function should_use_config_to_populate_segments()
    {
        $loader = new MappingLoader();
        $mappingDir = realpath(__DIR__ . '/../../src/EDI/Mapping');

        $segmentPopulator = $this->givenSegmentPopulator();

        $populator = $this->givenPopulator($segmentPopulator, $loader, $mappingDir);

        $fixtureDir = realpath(__DIR__ . '/../fixtures');
        $parser = new Parser();

        $data = $parser->parse($fixtureDir . '/invoic_message_standalone.edi');
        $messages = $populator->populate($data);

        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('INVOIC', $message->getIdentifier()['type']);
        
        $bgms = $message->getSegments('BGM');
        $this->assertCount(1, $bgms);
        $bgm = $bgms[0];
        $this->assertInstanceOf(Segment::class, $bgm);
        $this->assertEquals('BGM', $bgm->getCode());
    }

    /**
     * @param $loader
     * @param $mappingDir
     * @return MessagePopulator
     */
    private function givenPopulator(SegmentPopulator $segmentPopulator, $loader, $mappingDir)
    {
        $populator = new MessagePopulator(new AnnotationReader(), $segmentPopulator, $loader, $mappingDir);

        return $populator;
    }

    /**
     * @return mixed
     */
    protected function givenSegmentPopulator()
    {
        $segmentPopulator = \Phake::mock(SegmentPopulator::class);
        \Phake::when($segmentPopulator)->populate(\Phake::anyParameters())->thenReturnCallback(function (&$data) {
            $x = array_shift($data);
            $segment = new Segment($x[0]);
            $segment->setRawData($x);
            return $segment;
        });

        return $segmentPopulator;
    }
}
