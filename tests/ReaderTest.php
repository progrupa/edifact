<?php

namespace EDI\Tests;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Exception\MandatorySegmentPieceMissing;
use EDI\Mapping\MappingLoader;
use EDI\Message\Interchange;
use EDI\Parser;
use EDI\Populate\InterchangePopulator;
use EDI\Reader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_parse_incoming_data_and_pass_it_to_populator()
    {
        $fixtureDir = realpath(__DIR__ . '/fixtures');
        $testData = array('test_data');

        $parser = \Phake::mock(Parser::class);
        \Phake::when($parser)->parse(\Phake::anyParameters())->thenReturn($testData);
        $populator = \Phake::mock(InterchangePopulator::class);
        $expectedInterchange = new Interchange();
        \Phake::when($populator)->populate(\Phake::anyParameters())->thenReturn($expectedInterchange);
        $reader = $this->givenReader($parser, $populator);

        $interchange = $reader->transform($fixtureDir . '/valid_message.edi');

        \Phake::verify($parser)->parse($fixtureDir . '/valid_message.edi');
        \Phake::verify($populator)->populate($testData);
        $this->assertSame($expectedInterchange, $interchange);
    }

    /**
     * @param $mappingDir
     * @return Reader
     */
    protected function givenReader($parser, $populator)
    {
        $reader = new Reader($parser, $populator);

        return $reader;
    }
}
