<?php

namespace EDI\Populate;


use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Exception\MandatorySegmentPieceMissing;
use EDI\Exception\UnknownSegmentException;
use EDI\Mapping\DataElementMapping;
use EDI\Mapping\DataElementType;
use EDI\Mapping\SegmentMapping;
use EDI\Message\Segment;

class SegmentPopulator extends Populator
{
    /** @var SegmentMapping[] */
    private $segmentConfig = [];

    public function __construct()
    {
        parent::__construct(new AnnotationReader());
    }

    /**
     * @return \EDI\Mapping\SegmentMapping[]
     */
    public function getSegmentConfig()
    {
        return $this->segmentConfig;
    }

    /**
     * @param \EDI\Mapping\SegmentMapping[] $segmentConfig
     */
    public function setSegmentConfig($segmentConfig)
    {
        $this->segmentConfig = $segmentConfig;
    }

    /**
     * @param $data
     * @return Segment
     * @throws MandatorySegmentPieceMissing
     * @throws UnknownSegmentException
     */
    public function populate(&$data)
    {
        $segmentData = array_shift($data);
        $code = $segmentData[0];

        if (! isset($this->segmentConfig[$code])) {
            throw new UnknownSegmentException(sprintf("Unknown segment found: %s", $code));
        }
        $config = $this->segmentConfig[$code];
        $segment = new Segment();
        $segment->setCode($code);
        $segment->setRawData($segmentData);

        $values = $this->transformValues($config->getDataElements(), $segmentData);

        foreach ($values as $k => $v) {
            $segment->set($k, $v);
        }


        return $segment;
    }

    /**
     * @param $config
     * @param $segmentData
     * @param $values
     * @return mixed
     */
    protected function transformValues($dataElementsMapping, $segmentData)
    {
        $values = array();
        /** @var DataElementMapping $dataMapping */
        foreach ($dataElementsMapping as $i => $dataMapping) {
            if (! isset($segmentData[$i])) {
                if ($dataMapping->isRequired()) {
                    throw new MandatorySegmentPieceMissing(sprintf("Segment %s missing piece '%s'", $segmentData[0], $dataMapping->getName()));
                } else {
                    $dataElementValue = null;
                }
            } else {
                if ($dataMapping->getType() == DataElementType::COMPOSITE) {
                    $dataElementValue = $this->transformValues($dataMapping->getDataElements(), $segmentData[$i]);
                } else {
                    $dataElementValue = $segmentData[$i];
                }
            }
            $values[$dataMapping->getName()] = $dataElementValue;
        }

        return $values;
    }
}
