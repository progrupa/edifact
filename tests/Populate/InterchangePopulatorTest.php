<?php
/**
 * Created by PhpStorm.
 * User: dominikkasprzak
 * Date: 22/10/15
 * Time: 09:48
 */

namespace EDI\Tests\Populate;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Mapping\MappingLoader;
use EDI\Message\Interchange;
use EDI\Parser;
use EDI\Populate\InterchangePopulator;
use EDI\Populate\MessagePopulator;
use EDI\Populate\Populator;

class InterchangePopulatorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_transform_valid_message()
    {
        $populator = $this->givenPopulator();

        $fixtureDir = realpath(__DIR__ . '/../fixtures');
        $parser = new Parser();

        $data = $parser->parse($fixtureDir . '/valid_message.edi');
        $interchange = $populator->populate($data);

        $this->assertEquals('UNOC', $interchange->getSyntax()['name'], 'Interchange syntax name not correct');
        $this->assertEquals('3', $interchange->getSyntax()['version'], 'Interchange syntax version not correct');

        $trailer = $interchange->getTrailer();
        $this->assertEquals(1, $trailer->getControlCount(), 'Interchange control count not correct');
        $this->assertEquals(17, $trailer->getControlReference(), 'Interchange control reference not correct');
    }

    /**
     * @test
     * @expectedException \EDI\Exception\IncorrectSegmentId
     */
    public function should_detect_wrong_segment_identificator()
    {
        $populator = $this->givenPopulator();

        $fixtureDir = realpath(__DIR__ . '/../fixtures');
        $parser = new Parser();

        $data = $parser->parse($fixtureDir . '/invalid_message_wrong_segment.edi');
        $populator->populate($data);
    }

    /**
     * @test
     * @expectedException \EDI\Exception\MandatorySegmentPieceMissing
     */
    public function should_detect_mandatory_fields_missing()
    {
        $populator = $this->givenPopulator();

        $fixtureDir = realpath(__DIR__ . '/../fixtures');
        $parser = new Parser();

        $data = $parser->parse($fixtureDir . '/invalid_message_missing_value.edi');
        $populator->populate($data);
    }

    /**
     * @test
     * @expectedException \EDI\Exception\MandatorySegmentPieceMissing
     */
    public function should_detect_mandatory_field_part_missing()
    {
        $populator = $this->givenPopulator();

        $fixtureDir = realpath(__DIR__ . '/../fixtures');
        $parser = new Parser();

        $data = $parser->parse($fixtureDir . '/invalid_message_missing_value_part.edi');
        $populator->populate($data);
    }

    /**
     * @param $mappingLoader
     * @param $mappingDir
     * @return \EDI\Populate\InterchangePopulator
     */
    protected function givenPopulator()
    {
        $messagePopulator = \Phake::mock(MessagePopulator::class);
        \Phake::when($messagePopulator)->populate(\Phake::anyParameters())->thenReturnCallback(
            function (&$data) {
                while (count($data) > 1) array_shift($data);
                return array();
            }
        );
        $populator = new InterchangePopulator(new AnnotationReader(), $messagePopulator);

        return $populator;
    }

}
