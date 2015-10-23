<?php

namespace EDI\Populate;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Annotations\Mandatory;
use EDI\Annotations\Segment;
use EDI\Annotations\SegmentPiece;
use EDI\Exception\IncorrectSegmentId;
use EDI\Exception\MandatorySegmentPieceMissing;
use EDI\Mapping;
use EDI\Mapping\CodeMapping;
use EDI\Mapping\SegmentMapping;

abstract class Populator
{

    /** @var  AnnotationReader */
    private $annotationReader;
//    /** @var  SegmentMapping[] Segments defined in active mapping */
//    private $segments;
//    /** @var  CodeMapping[] Data element fields descriptions */
//    private $codes;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

//    /**
//     * @return Mapping\SegmentMapping[]
//     */
//    public function getSegments()
//    {
//        return $this->segments;
//    }
//
//    /**
//     * @param Mapping\SegmentMapping[] $segments
//     */
//    public function setSegments($segments)
//    {
//        $this->segments = $segments;
//    }
//
//    /**
//     * @return Mapping\CodeMapping[]
//     */
//    public function getCodes()
//    {
//        return $this->codes;
//    }
//
//    /**
//     * @param Mapping\CodeMapping[] $codes
//     */
//    public function setCodes($codes)
//    {
//        $this->codes = $codes;
//    }

    abstract public function populate(&$data);

    protected function fillProperties($object, &$data)
    {
        $segmentData = array_shift($data);
        $classRefl = new \ReflectionClass($object);

        //  Check if proper segment was received
        if ($segmentAnnotation = $this->annotationReader->getClassAnnotation($classRefl, Segment::class)) {
            if ($segmentData[0] != $segmentAnnotation->value) {
                throw new IncorrectSegmentId(sprintf("Expected %s segment, %s found", $segmentAnnotation->value, $segmentData[0]));
            }
        }
        //  Populate SegmentPiece properties
        foreach ($classRefl->getProperties() as $propRefl) {
            $isSegmentPiece = $this->annotationReader->getPropertyAnnotation($propRefl, SegmentPiece::class);
            if ($isSegmentPiece) {
                $piece = $segmentData[$isSegmentPiece->position];
                $propRefl->setAccessible(true);
                if ($isSegmentPiece->parts) {
                    $value = array();
                    $i = 0;
                    foreach ($isSegmentPiece->parts as $k => $part) {
                        if (!is_numeric($k) && is_array($part)) {
                            $partName = $k;
                            if (in_array("@mandatory", $part) && empty($piece[$i])) {
                                throw new MandatorySegmentPieceMissing(sprintf("Segment %s part %s missing value at offset %d",
                                    $segmentData[0], $partName, $i));
                            }
                        } else {
                            $partName = $part;
                        }
                        $value[$partName] = isset($piece[$i]) ? $piece[$i] : null;
                        ++$i;
                    }
                    $propRefl->setValue($object, $value);
                } else {
                    $propRefl->setValue($object, $piece);
                }
            }
        }
        //  Check if mandatory fields have values
        foreach ($classRefl->getProperties() as $propRefl) {
            $propRefl->setAccessible(true);
            $isMandatory = $this->annotationReader->getPropertyAnnotation($propRefl, Mandatory::class);
            if ($isMandatory && empty($propRefl->getValue($object))) {
                throw new MandatorySegmentPieceMissing(sprintf("Segment %s missing mandatory property %s value",
                    $segmentData[0], $propRefl->getName()));
            }
        }
    }
}
