<?php

namespace EDI\Mapping;

class MappingLoader
{
    public function loadMessage($mapping)
    {
        $segmentsXml = simplexml_load_file($mapping);

        $messageMapping = new MessageMapping();
        $messageMapping->setSegments($this->createMessageSegments($segmentsXml->children(), $messageMapping));

        return $messageMapping;
    }

    public function loadSegments($mapping)
    {
        $segmentsXml = simplexml_load_file($mapping);
        $segments = array();
        /** @var \SimpleXMLElement $segmentXml */
        foreach ($segmentsXml->children() as $segmentXml) {
            $segment = new SegmentMapping((string) $segmentXml->attributes()->id);
            $i = 1;
            /** @var \SimpleXMLElement $dataElementXml */
            foreach ($segmentXml->children() as $dataElementXml) {
                $dataElement = $this->createDataElement($dataElementXml);

                $segment->addDataElement($i, $dataElement);
                $i++;
            }

            $segments[$segment->getId()] = $segment;
        }

        return $segments;
    }

    public function loadCodes($file)
    {
        $elementsXml = simplexml_load_file($file);
        $codes = array();
        /** @var \SimpleXMLElement $codesXml */
        foreach ($elementsXml->children() as $codesXml) {
            $id = (string) $codesXml->attributes()->id;
            foreach ($codesXml->children() as $codeXml) {
                $codes[$id][] = new CodeMapping($codeXml->attributes()->id, $codeXml->attributes()->desc);
            }
        }

        return $codes;
    }

    /**
     * @param $dataElementXml
     * @return DataElementMapping
     */
    protected function createDataElement(\SimpleXMLElement $dataElementXml)
    {
        if ($dataElementXml->getName() == 'composite_data_element') {
            $dataElement = new CompositeDataElementMapping(
                $dataElementXml->attributes()->id,
                $dataElementXml->attributes()->required
            );
            foreach ($dataElementXml->children() as $child) {
                $dataElement->addDataElement($this->createDataElement($child));
            }
        } else {
            $dataElement = new DataElementMapping(
                $dataElementXml->attributes()->id,
                $dataElementXml->attributes()->required
            );
        }

        return $dataElement;
    }

    /**
     * @param \SimpleXMLElement[] $segmentsXMLElements
     * @param MessageMapping $messageMapping
     * @return array
     */
    protected function createMessageSegments($segmentsXMLElements, $messageMapping)
    {
        $segments = array();
        foreach ($segmentsXMLElements as $segmentXml) {
            if ($segmentXml->getName() == 'defaults') {
                $defaults = array();
                foreach ($segmentXml->children() as $default) {
                    $defaults[(string)$default->attributes()->id] = (string)$default->attributes()->value;
                }
                $messageMapping->setDefaults($defaults);
            } else {
                if ($segmentXml->getName() == 'group') {
                    $segment = new MessageSegmentGroupMapping(
                        (string)$segmentXml->attributes()->id,
                        (string)$segmentXml->attributes()->maxrepeat,
                        is_null($segmentXml->attributes()->required) ? false : true
                    );
                    $segment->setSegmentMappings($this->createMessageSegments($segmentXml->children(), $messageMapping));
                } else {
                    $segment = new MessageSegmentMapping(
                        (string)$segmentXml->attributes()->id,
                        (string)$segmentXml->attributes()->maxrepeat,
                        is_null($segmentXml->attributes()->required) ? false : true
                    );
                }
                $segments[] = $segment;
            }
        }

        return $segments;
    }
}
