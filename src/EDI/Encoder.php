<?php

namespace EDI;

use EDI\Mapping\SegmentMapping;
use EDI\Message\Interchange;
use EDI\Printer\AnnotationPrinter;
use EDI\Printer\SegmentPrinter;

class Encoder
{
    /** @var  AnnotationPrinter */
    private $annotationPrinter;
    /** @var  SegmentPrinter */
    private $segmentPrinter;

    public function __construct(AnnotationPrinter $annotationPrinter, SegmentPrinter $segmentPrinter)
    {
        $this->annotationPrinter = $annotationPrinter;
        $this->segmentPrinter = $segmentPrinter;
    }


    public function encode(Interchange $interchange)
    {
        $out = $this->annotationPrinter->prepareString($interchange);

        foreach ($interchange->getMessages() as $message) {
            $out .= $this->annotationPrinter->prepareString($message);
            foreach ($message->getSegments() as $segment) {
                $out .= $this->segmentPrinter->prepareString($segment);
            }
            $out .= $this->annotationPrinter->prepareString($message->createTrailer());
        }

        $out .= $this->annotationPrinter->prepareString($interchange->createTrailer());
        return $out;
    }
}
