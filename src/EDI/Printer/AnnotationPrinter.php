<?php

namespace EDI\Printer;


use EDI\Annotations\Segment;
use EDI\Annotations\SegmentPiece;
use EDI\Exception\AnnotationMissing;

class AnnotationPrinter extends Printer
{
    public function __construct() {}

    protected function getProperties($object)
    {
        $reflClass = new \ReflectionClass($object);

        $segmentAttrs = $reflClass->getAttributes(Segment::class);
        if (empty($segmentAttrs)) {
            throw new AnnotationMissing(sprintf("Missing #[Segment] attribute for class %s", $reflClass->getName()));
        }
        $codeAnnotation = $segmentAttrs[0]->newInstance();

        $properties = [$codeAnnotation->value];

        foreach ($reflClass->getProperties() as $propRefl) {
            $propRefl->setAccessible(true);
            $pieceAttrs = $propRefl->getAttributes(SegmentPiece::class);
            if (!empty($pieceAttrs)) {
                /** @var SegmentPiece $isSegmentPiece */
                $isSegmentPiece = $pieceAttrs[0]->newInstance();
                if (! $isSegmentPiece->parts) {
                    $properties[$isSegmentPiece->position] = $propRefl->getValue($object);
                } else {
                    $parts = $isSegmentPiece->parts;
                    $value = $propRefl->getValue($object);
                    $valuePieces = [];
                    foreach ($parts as $key => $part) {
                        if (is_integer($key)) {
                            $partName = $part;
                        } else {
                            $partName = $key;
                        }
                        $valuePieces[] = isset($value[$partName]) ? $value[$partName] : null;
                    }
                    $properties[$isSegmentPiece->position] = $this->weedOutEmpty($valuePieces);
                }
            }
        }

        $properties = $this->weedOutEmpty($properties);

        return $properties;
    }
}
