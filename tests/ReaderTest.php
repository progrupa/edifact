<?php

namespace EDI\Tests;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Mapping\MappingLoader;
use EDI\Parser;
use EDI\Reader;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_transform_valid_message()
    {
        $mappingDir = realpath(__DIR__ . '/../src/EDI/Mapping/D96A');
        $fixtureDir = realpath(__DIR__ . '/fixtures');
        $parser = new Parser();
        $reader = new Reader(new AnnotationReader(), $parser, new MappingLoader(), $mappingDir);

        $interchange = $reader->transform($fixtureDir . '/valid_message.edi');
        $this->assertEquals('UNOC', $interchange->getSyntax()['name']);
        $this->assertEquals('3', $interchange->getSyntax()['version']);
    }
}
