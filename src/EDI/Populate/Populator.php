<?php

namespace EDI\Populate;


use EDI\Annotations\Mandatory;
use EDI\Annotations\Segment;
use EDI\Annotations\SegmentPiece;
use EDI\Exception\IncorrectSegmentId;
use EDI\Exception\MandatorySegmentPieceMissing;
use EDI\Mapping;
use EDI\Message\Segment as MessageSegment;

abstract class Populator
{
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
     * @param \ReflectionClass $classRefl
     * @param $segmentCode
     * @throws MandatorySegmentPieceMissing
     */
    protected function checkMandatoryFields($object, \ReflectionClass $classRefl = null, $segmentCode)
    {
        foreach ($classRefl->getProperties() as $propRefl) {
            $propRefl->setAccessible(true);
            $attrs = $propRefl->getAttributes(Mandatory::class);
            if (!empty($attrs) && $this->isEmpty($propRefl->getValue($object))) {
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
        $attrs = $classRefl->getAttributes(Segment::class);
        if (!empty($attrs)) {
            $segmentAnnotation = $attrs[0]->newInstance();
            if ($segmentCode != $segmentAnnotation->value) {
                throw new IncorrectSegmentId(sprintf("Expected %s segment, %s found", $segmentAnnotation->value, $segmentCode));
            }
        }
    }

    /**
     * @param $object
     * @param \ReflectionClass $classRefl
     * @param $segmentData
     * @throws MandatorySegmentPieceMissing
     */
    protected function fillFromArray($object, $classRefl, $segmentData)
    {
        foreach ($classRefl->getProperties() as $propRefl) {
            $attrs = $propRefl->getAttributes(SegmentPiece::class);
            if (!empty($attrs)) {
                $isSegmentPiece = $attrs[0]->newInstance();
                $piece = isset($segmentData[$isSegmentPiece->position]) ? $segmentData[$isSegmentPiece->position] : null;
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
