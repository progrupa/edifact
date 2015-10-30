<?php

namespace EDI;

use EDI\Mapping\MappingLoader;
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
    /** @var  MappingLoader */
    private $mappingLoader;

    public function __construct(AnnotationPrinter $annotationPrinter, SegmentPrinter $segmentPrinter, MappingLoader $mappingLoader)
    {
        $this->annotationPrinter = $annotationPrinter;
        $this->segmentPrinter = $segmentPrinter;
        $this->mappingLoader = $mappingLoader;
    }


    public function encode(Interchange $interchange)
    {
        $out = $this->annotationPrinter->prepareString($interchange);

        foreach ($interchange->getMessages() as $message) {
            $out .= $this->annotationPrinter->prepareString($message);
            $identifier = $message->getIdentifier();
            $this->segmentPrinter->setSegmentMappings($this->mappingLoader->loadSegments($identifier['version'], $identifier['release']));

            foreach ($message->getSegments() as $segment) {
                $out .= $this->segmentPrinter->prepareString($segment);
            }
            $out .= $this->annotationPrinter->prepareString($message->createTrailer());
        }

        $out .= $this->annotationPrinter->prepareString($interchange->createTrailer());
        return $out;
    }
}
