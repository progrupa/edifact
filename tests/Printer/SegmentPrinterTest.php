<?php
/**
 * Created by PhpStorm.
 * User: dominikkasprzak
 * Date: 29/10/15
 * Time: 13:40
 */

namespace EDI\Tests\Printer;


use EDI\Mapping\CompositeDataElementMapping;
use EDI\Mapping\DataElementMapping;
use EDI\Mapping\DataElementType;
use EDI\Mapping\SegmentMapping;
use EDI\Message\Segment;
use EDI\Printer\SegmentPrinter;

class SegmentPrinterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_use_config_to_print_segment()
    {
        $printer = $this->givenPrinter();

        $segment = new Segment('XXX');
        $segment->ajdi = 111;
        $segment->fild = 222;
        $segment->araj = ['e1' => 333, 'e2' => 444];

        $this->assertEquals("XXX+111+222+333:444'", $printer->prepareString($segment));
    }

    /** @test */
    public function should_weed_out_empty_fields()
    {
        $printer = $this->givenPrinter();

        $segment = new Segment('XXX');
        $segment->ajdi = 111;
        $segment->fild = null;
        $segment->araj = ['e2' => 444];

        $this->assertEquals("XXX+111++:444'", $printer->prepareString($segment));

        $segment->araj = [];

        $this->assertEquals("XXX+111'", $printer->prepareString($segment));
    }

    /**
     * @return SegmentPrinter
     */
    protected function givenPrinter()
    {
        $segmentMapping = new SegmentMapping('XXX');
        $segmentMapping->addDataElement(1, new DataElementMapping(5434, true, DataElementType::ID, 'ajdi'));
        $segmentMapping->addDataElement(2, new DataElementMapping(4525, true, DataElementType::A, 'fild'));

        $compDataElement = new CompositeDataElementMapping(2435, true, DataElementType::COMPOSITE, 'araj');
        $compDataElement->addDataElement(new DataElementMapping(6456, true, DataElementType::A, 'e1'));
        $compDataElement->addDataElement(new DataElementMapping(3567, true, DataElementType::A, 'e2'));
        $segmentMapping->addDataElement(3, $compDataElement);

        $printer = new SegmentPrinter(['XXX' => $segmentMapping]);

        return $printer;
    }
}
