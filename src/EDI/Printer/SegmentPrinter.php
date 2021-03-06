<?php

namespace EDI\Printer;

use EDI\Exception\Exception;
use EDI\Mapping\CompositeDataElementMapping;
use EDI\Mapping\SegmentMapping;
use EDI\Message\Segment;
use EDI\Message\SegmentGroup;

class SegmentPrinter extends Printer
{
    /** @var  SegmentMapping[] */
    private $segmentMappings;

    /**
     * @return \EDI\Mapping\SegmentMapping[]
     */
    public function getSegmentMappings()
    {
        return $this->segmentMappings;
    }

    /**
     * @param \EDI\Mapping\SegmentMapping[] $segmentMappings
     */
    public function setSegmentMappings($segmentMappings)
    {
        $this->segmentMappings = $segmentMappings;
    }

    public function prepareString($object)
    {
        if ($object instanceof SegmentGroup) {
            $out = '';
            foreach ($object->getSegments() as $seg) {
                $out .= $this->prepareString($seg);
            }

            return $out;
        } else {
            return parent::prepareString($object);
        }
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
                foreach ($dataElement->getDataElements() as $i => $childElement) {
                    $values[] = isset($actualValues[$childElement->getName()]) ? $actualValues[$childElement->getName()] : (isset($actualValues[$i]) ? $actualValues[$i] : null);
                }
                $properties[] = $this->weedOutEmpty($values);
            } else {
                $properties[] = $object->get($dataElement->getName());
            }
        }

        return $this->weedOutEmpty($properties);
    }
}
