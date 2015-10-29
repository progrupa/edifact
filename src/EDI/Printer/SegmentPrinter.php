<?php

namespace EDI\Printer;

use EDI\Exception\Exception;
use EDI\Mapping\CompositeDataElementMapping;
use EDI\Mapping\SegmentMapping;
use EDI\Message\Segment;

class SegmentPrinter extends Printer
{
    /** @var  SegmentMapping[] */
    private $segmentMappings;

    public function __construct($segmentMappings)
    {
        $this->segmentMappings = $segmentMappings;
    }

    protected function getProperties($object)
    {
        if (! $object instanceof Segment) {
            throw new Exception(sprintf("Only Segment objects are supported, %s given", get_class($object)));
        }

        $code = $object->getCode();
        $mapping = $this->segmentMappings[$code];

        $properties = [$code];

        foreach ($mapping->getDataElements() as $dataElement) {
            if ($dataElement instanceof CompositeDataElementMapping) {
                $actualValues = $object->get($dataElement->getName());
                $values = [];
                foreach ($dataElement->getDataElements() as $childElement) {
                    $values[] = isset($actualValues[$childElement->getName()]) ? $actualValues[$childElement->getName()] : null;
                }
                $properties[] = $this->weedOutEmpty($values);
            } else {
                $properties[] = $object->get($dataElement->getName());
            }
        }

        return $this->weedOutEmpty($properties);
    }
}
