<?php

namespace EDI\Mapping;

class MappingLoader
{
    public function loadMessage($mapping)
    {
        return new MessageMapping();
//        $segmentsXml = simplexml_load_file($mapping);
//        $segments = array();
//        /** @var \SimpleXMLElement $segmentXml */
//        foreach ($segmentsXml->children() as $segmentXml) {
//            $segment = new SegmentMapping((string) $segmentXml->attributes()->id);
//            /** @var \SimpleXMLElement $dataElementXml */
//            foreach ($segmentXml->children() as $dataElementXml) {
//                $dataElement = $this->createDataElement($dataElementXml);
//
//                $segment->addDataElement($dataElement);
//            }
//
//            $segments[$segment->getId()] = $segment;
//        }
//
//        return $segments;
    }

    public function loadSegments($mapping)
    {
        $segmentsXml = simplexml_load_file($mapping);
        $segments = array();
        /** @var \SimpleXMLElement $segmentXml */
        foreach ($segmentsXml->children() as $segmentXml) {
            $segment = new SegmentMapping((string) $segmentXml->attributes()->id);
            /** @var \SimpleXMLElement $dataElementXml */
            foreach ($segmentXml->children() as $dataElementXml) {
                $dataElement = $this->createDataElement($dataElementXml);

                $segment->addDataElement($dataElement);
            }

            $segments[$segment->getId()] = $segment;
        }

        return $segments;
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
}
