<?php

namespace EDI\Tests\Printer;

use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Annotations;
use EDI\Exception\AnnotationMissing;
use EDI\Printer\AnnotationPrinter;

class AnnotationPrinterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_read_annotations_for_data()
    {
        $printer = new AnnotationPrinter(new AnnotationReader());

        $this->assertEquals("XXX+x+y:z+a'", $printer->prepareString(new Correct('x', ['piece1' => 'y', 'piece2' => 'z'], 'a')));
        $this->assertEquals("XXX+x+y:z+a'", $printer->prepareString(new Correct('x', ['piece2' => 'z', 'piece1' => 'y'], 'a')));
    }

    /** @test */
    public function should_allow_omitting_non_mandatory_fields()
    {
        $printer = new AnnotationPrinter(new AnnotationReader());

        $this->assertEquals("XXX+x'", $printer->prepareString(new Correct('x')));
        $this->assertEquals("XXX+x++a'", $printer->prepareString(new Correct('x', null, 'a')));
        $this->assertEquals("XXX+x+y:z'", $printer->prepareString(new Correct('x', ['piece1' => 'y', 'piece2' => 'z'])));
        $this->assertEquals("XXX+x+y'", $printer->prepareString(new Correct('x', ['piece1' => 'y'])));
        $this->assertEquals("XXX+x+:z'", $printer->prepareString(new Correct('x', ['piece2' => 'z'])));
    }

    /** @test */
    public function should_support_a_gap_in_annotations()
    {
        $printer = new AnnotationPrinter(new AnnotationReader());

        $this->assertEquals("XXX+x++a'", $printer->prepareString(new CorrectWithGap('x', 'a')));
    }

    /**
     * @test
     * @expectedException \EDI\Exception\AnnotationMissing
     */
    public function should_except_when_annotation_missing()
    {
        $printer = new AnnotationPrinter(new AnnotationReader());

        $printer->prepareString(new Incorrect());
    }
}

/** @Annotations\Segment("XXX") */
class Correct
{
    /**
     * @Annotations\SegmentPiece(position="1")
     * @Annotations\Mandatory()
     */
    private $field;
    /**
     * @Annotations\SegmentPiece(position="2", parts={"piece1", "piece2"})
     */
    private $another;
    /**
     * @Annotations\SegmentPiece(position="3")
     */
    private $final;

    public function __construct($field, $another = null, $final = null)
    {
        $this->field = $field;
        $this->another = $another;
        $this->final = $final;
    }
}

/** @Annotations\Segment("XXX") */
class CorrectWithGap
{
    /**
     * @Annotations\SegmentPiece(position="1")
     * @Annotations\Mandatory()
     */
    private $field;
    /**
     * @Annotations\SegmentPiece(position="3")
     */
    private $final;

    public function __construct($field, $final = null)
    {
        $this->field = $field;
        $this->final = $final;
    }
}

class Incorrect {}
