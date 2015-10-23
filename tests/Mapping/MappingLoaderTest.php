<?php

namespace EDI\Tests\Mapping;

use EDI\Mapping\CodeMapping;
use EDI\Mapping\CompositeDataElementMapping;
use EDI\Mapping\MappingLoader;
use EDI\Mapping\SegmentMapping;

class MappingLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_load_mapping_from_file()
    {
        $file = realpath(__DIR__.'/../fixtures/segments.xml');

        $loader = new MappingLoader();

        $result = $loader->loadSegments($file);

        $this->assertEquals(1, count($result));
        /** @var SegmentMapping $segment */
        $segment = $result['ADR'];
        $this->assertEquals('ADR', $segment->getId());
        //  Check data elements
        $dataElements = $segment->getDataElements();
        $this->assertEquals(2, count($dataElements));
        $this->assertEquals('3164', $dataElements[0]->getId());
        $this->assertEquals('C517', $dataElements[1]->getId());
        //  Check composite data elements
        $this->assertInstanceOf(CompositeDataElementMapping::class, $dataElements[1]);
        $compositeElements = $dataElements[1]->getDataElements();
        $this->assertEquals('3225', $compositeElements[0]->getId());
        $this->assertEquals('1131', $compositeElements[1]->getId());
    }

    /** @test */
    public function should_load_codes_from_file()
    {
        $file = realpath(__DIR__.'/../fixtures/codes.xml');

        $loader = new MappingLoader();

        $result = $loader->loadCodes($file);

        $this->assertEquals(1, count($result));
        $this->assertEquals('1', $result['1001'][0]->getId());
        $this->assertEquals('Certificate of analysis', $result['1001'][0]->getDesc());
        $this->assertEquals('2', $result['1001'][1]->getId());
        $this->assertEquals('Certificate of conformity', $result['1001'][1]->getDesc());
        $this->assertEquals('3', $result['1001'][2]->getId());
        $this->assertEquals('Certificate of quality', $result['1001'][2]->getDesc());
    }
}
