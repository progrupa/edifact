<?php

namespace EDI\Populate;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Annotations\Mandatory;
use EDI\Annotations\Segment;
use EDI\Annotations\SegmentPiece;
use EDI\Exception\IncorrectSegmentId;
use EDI\Exception\MandatorySegmentPieceMissing;
use EDI\Mapping;
use EDI\Message\Segment as MessageSegment;

abstract class Populator
{
    /** @var  AnnotationReader */
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    abstract public function populate(&$data);

    protected function fillProperties($object, &$data)
    {
        if ($data instanceof MessageSegment) {
            $this->fromSegment($object, $data);
        } else {
            $segmentData = array_shift($data);
            $classRefl = new \ReflectionClass($object);

            //  Check if proper segment was received
            $this->checkSegmentCode($classRefl, $segmentData[0]);

            //  Populate SegmentPiece properties
            $this->fillFromArray($object, $classRefl, $segmentData);

            //  Check if mandatory fields have values
            $this->checkMandatoryFields($object, $classRefl, $segmentData[0]);
        }
    }

    protected function fromSegment($object, MessageSegment $segment)
    {
        $classRefl = new \ReflectionClass($object);

        //  Check if proper segment was received
        $this->checkSegmentCode($classRefl, $segment->getCode());

        //  Populate SegmentPiece properties
        $this->fillFromArray($object, $classRefl, array_values($segment->getRawData()));

        //  Check if mandatory fields have values
        $this->checkMandatoryFields($object, $classRefl, $segment->getCode());
    }

    /**
     * @param $object
     * @param $classRefl
     * @param $segmentCode
     * @throws MandatorySegmentPieceMissing
     */
    protected function checkMandatoryFields($object, \ReflectionClass $classRefl = null, $segmentCode)
    {
        foreach ($classRefl->getProperties() as $propRefl) {
            $propRefl->setAccessible(true);
            $isMandatory = $this->annotationReader->getPropertyAnnotation($propRefl, Mandatory::class);
            if ($isMandatory && $this->isEmpty($propRefl->getValue($object))) {
                throw new MandatorySegmentPieceMissing(sprintf("Segment %s missing mandatory property %s value",
                    $segmentCode, $propRefl->getName()));
            }
        }
    }

    /**
     * @param \ReflectionClass $classRefl
     * @param string $segmentCode
     * @throws IncorrectSegmentId
     */
    protected function checkSegmentCode($classRefl, $segmentCode)
    {
        if ($segmentAnnotation = $this->annotationReader->getClassAnnotation($classRefl, Segment::class)) {
            if ($segmentCode != $segmentAnnotation->value) {
                throw new IncorrectSegmentId(sprintf("Expected %s segment, %s found", $segmentAnnotation->value, $segmentCode));
            }
        }
    }

    /**
     * @param $object
     * @param $classRefl
     * @param $segmentData
     * @throws MandatorySegmentPieceMissing
     */
    protected function fillFromArray($object, $classRefl, $segmentData)
    {
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
                            if (!empty($piece) && in_array("@mandatory", $part) && $this->isEmpty($piece[$i])) {
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
    }

    private function isEmpty($i)
    {
        if (is_array($i)) {
            return empty(array_filter($i, function($a) {return !is_null($a);}));
        }
        return empty($i);
    }
}
