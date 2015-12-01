<?php

namespace EDI\Mapping;

class MappingLoader
{
    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function loadMessage($version, $release, $message)
    {
        $messageXml = simplexml_load_file(sprintf('%s/%s%s/messages/%s.xml', $this->dir, strtoupper($version), strtoupper($release), strtolower($message)));

        $messageMapping = new MessageMapping();
        $messageMapping->setSegments($this->createMessageSegments($messageXml->children(), $messageMapping));

        return $messageMapping;
    }

    public function loadSegments($version, $release)
    {
        $segmentsXml = simplexml_load_file(sprintf('%s/%s%s/segments.xml', $this->dir, strtoupper($version), strtoupper($release)));
        $segments = array();
        /** @var \SimpleXMLElement $segmentXml */
        foreach ($segmentsXml->children() as $segmentXml) {
            $segment = new SegmentMapping((string) $segmentXml->attributes()->id);
            $segment->setName((string) $segmentXml->attributes()->name);
            $segment->setDesc((string) $segmentXml->attributes()->desc);
            $i = 1;
            /** @var \SimpleXMLElement $dataElementXml */
            foreach ($segmentXml->children() as $dataElementXml) {
                $dataElement = $this->createDataElement($dataElementXml);

                $segment->addDataElement($i, $dataElement);
                $i++;
            }

            $segments[$segment->getId()] = $segment;
        }
        $segments['UNS'] = new SegmentMapping('UNS');
        $segments['UNS']->addDataElement(1, new DataElementMapping('0081', true, DataElementType::A, 'id', null, 1));

        return $segments;
    }

    public function loadCodes($version, $release)
    {
        $elementsXml = simplexml_load_file(sprintf('%s/%s%s/codes.xml', $this->dir, strtoupper($version), strtoupper($release)));
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
        $dataElement->setName((string) $dataElementXml->attributes()->name);
        $dataElement->setDesc((string) $dataElementXml->attributes()->desc);
        $dataElement->setType((string) $dataElementXml->attributes()->type);
        $dataElement->setMaxLength((int) $dataElementXml->attributes()->maxlength);

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
