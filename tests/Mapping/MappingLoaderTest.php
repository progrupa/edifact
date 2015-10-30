<?php

namespace EDI\Tests\Mapping;

use EDI\Mapping\CodeMapping;
use EDI\Mapping\CompositeDataElementMapping;
use EDI\Mapping\MappingLoader;
use EDI\Mapping\MessageMapping;
use EDI\Mapping\MessageSegmentGroupMapping;
use EDI\Mapping\MessageSegmentMapping;
use EDI\Mapping\SegmentMapping;

class MappingLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_load_segment_mapping_from_file()
    {
        $loader = new MappingLoader(__DIR__.'/../fixtures');

        $result = $loader->loadSegments('D', '96A');

        $this->assertEquals(1, count($result));
        /** @var SegmentMapping $segment */
        $segment = $result['ADR'];
        $this->assertEquals('ADR', $segment->getId());
        //  Check data elements
        $dataElements = $segment->getDataElements();
        $this->assertEquals(2, count($dataElements));
        $this->assertEquals('3164', $dataElements[1]->getId());
        $this->assertEquals('C517', $dataElements[2]->getId());
        //  Check composite data elements
        $this->assertInstanceOf(CompositeDataElementMapping::class, $dataElements[2]);
        $compositeElements = $dataElements[2]->getDataElements();
        $this->assertEquals('3225', $compositeElements[0]->getId());
        $this->assertEquals('1131', $compositeElements[1]->getId());
    }

    /** @test */
    public function should_load_codes_from_file()
    {
        $loader = new MappingLoader(__DIR__.'/../fixtures');

        $result = $loader->loadCodes('D', '96A');

        $this->assertEquals(1, count($result));
        $this->assertEquals('1', $result['1001'][0]->getId());
        $this->assertEquals('Certificate of analysis', $result['1001'][0]->getDesc());
        $this->assertEquals('2', $result['1001'][1]->getId());
        $this->assertEquals('Certificate of conformity', $result['1001'][1]->getDesc());
        $this->assertEquals('3', $result['1001'][2]->getId());
        $this->assertEquals('Certificate of quality', $result['1001'][2]->getDesc());
    }

    /** @test */
    public function should_load_messages_from_file()
    {
        $loader = new MappingLoader(__DIR__.'/../fixtures');

        $result = $loader->loadMessage('D', '96A', 'MESSAGE');

        $this->assertInstanceOf(MessageMapping::class, $result);

        $defaults = $result->getDefaults();
        $this->assertEquals('INVOIC', $defaults['0065']);
        $this->assertEquals('D', $defaults['0052']);
        $this->assertEquals('96A', $defaults['0054']);
        $this->assertEquals('UN', $defaults['0051']);

        $segments = $result->getSegments();
        $this->assertInstanceOf(MessageSegmentMapping::class, $segments[0]);
        $this->assertEquals('UNH', $segments[0]->getCode());
        $this->assertEquals(1, $segments[0]->getMaxRepeat());
        $this->assertEquals(true, $segments[0]->isRequired());

        $this->assertInstanceOf(MessageSegmentGroupMapping::class, $segments[7]);
        $this->assertEquals('SG1', $segments[7]->getCode());
        $this->assertEquals(99, $segments[7]->getMaxRepeat());
        $this->assertEquals(false, $segments[7]->isRequired());

        $groupSegments = $segments[7]->getSegmentMappings();
        $this->assertInstanceOf(MessageSegmentMapping::class, $groupSegments[0]);
        $this->assertEquals('RFF', $groupSegments[0]->getCode());
        $this->assertEquals(1, $groupSegments[0]->getMaxRepeat());
        $this->assertEquals(true, $groupSegments[0]->isRequired());
    }
}
