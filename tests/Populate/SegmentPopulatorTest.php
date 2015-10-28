<?php

namespace EDI\Tests\Populate;


use EDI\Mapping\CompositeDataElementMapping;
use EDI\Mapping\DataElementMapping;
use EDI\Mapping\DataElementType;
use EDI\Mapping\SegmentMapping;
use EDI\Populate\SegmentPopulator;

class SegmentPopulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \EDI\Exception\UnknownSegmentException
     */
    public function should_throw_exception_if_unknown_segment()
    {
        $populator = new SegmentPopulator([]);

        $data = [['XXX', '345', 'some data', ['whatever', 'content']]];
        $populator->populate($data);
    }

    /** @test */
    public function should_populate_according_to_config()
    {
        $segmentMapping = new SegmentMapping('XXX');
        $segmentMapping->addDataElement(1, new DataElementMapping(7583, /* required */true, DataElementType::ID, 'ajdi', '', 3));
        $segmentMapping->addDataElement(2, new DataElementMapping(3415, /* required */false, DataElementType::A, 'field', '', 35));

        $compDataElement = new CompositeDataElementMapping(7583, /* required */false, DataElementType::COMPOSITE, 'comp');
        $compDataElement->addDataElement(new DataElementMapping(1111, /* required */false, DataElementType::A, 'ajnc'));
        $compDataElement->addDataElement(new DataElementMapping(2222, /* required */false, DataElementType::A, 'cfaj'));
        $segmentMapping->addDataElement(3, $compDataElement);
        $populator = new SegmentPopulator(['XXX' => $segmentMapping]);

        $data = [['XXX', '345', 'some data', ['whatever', 'content']]];
        $segment = $populator->populate($data);

        $this->assertEquals(345, $segment->ajdi);
        $this->assertEquals('some data', $segment->field);
        $this->assertEquals('whatever', $segment->comp['ajnc']);
        $this->assertEquals('content', $segment->comp['cfaj']);
    }

    /**
     * @test
     * @expectedException \EDI\Exception\MandatorySegmentPieceMissing
     */
    public function should_check_required_fields()
    {
        $segmentMapping = new SegmentMapping('XXX');
        $segmentMapping->addDataElement(1, new DataElementMapping(7583, /* required */true, DataElementType::ID, 'ajdi', '', 3));
        $segmentMapping->addDataElement(2, new DataElementMapping(3415, /* required */true, DataElementType::A, 'field', '', 35));
        $populator = new SegmentPopulator(['XXX' => $segmentMapping]);

        $data = [['XXX', '345']];
        $populator->populate($data);
    }

    /** @test */
    public function should_ignore_missing_nonrequired_fields()
    {
        $segmentMapping = new SegmentMapping('XXX');
        $segmentMapping->addDataElement(1, new DataElementMapping(7583, /* required */true, DataElementType::ID, 'ajdi', '', 3));
        $segmentMapping->addDataElement(2, new DataElementMapping(3415, /* required */false, DataElementType::A, 'field', '', 35));
        $populator = new SegmentPopulator(['XXX' => $segmentMapping]);

        $data = [['XXX', '345']];
        $segment = $populator->populate($data);

        $this->assertEquals(345, $segment->ajdi);
        $this->assertNull($segment->field);
    }
}
