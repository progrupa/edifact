<?php

namespace EDI\Printer;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Annotations\Segment;
use EDI\Annotations\SegmentPiece;
use EDI\Exception\AnnotationMissing;

class AnnotationPrinter extends Printer
{
    /** @var  AnnotationReader */
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    protected function getProperties($object)
    {
        $reflClass = new \ReflectionClass($object);

        $codeAnnotation = $this->annotationReader->getClassAnnotation($reflClass, Segment::class);
        if (! $codeAnnotation) {
            throw new AnnotationMissing(sprintf("Missing @Segment annotation for class %", $reflClass->getName()));
        }

        $properties = [$codeAnnotation->value];

        foreach ($reflClass->getProperties() as $propRefl) {
            $propRefl->setAccessible(true);
            /** @var SegmentPiece $isSegmentPiece */
            $isSegmentPiece = $this->annotationReader->getPropertyAnnotation($propRefl, SegmentPiece::class);
            if ($isSegmentPiece) {
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
