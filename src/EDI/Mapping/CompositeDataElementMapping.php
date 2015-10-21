<?php

namespace EDI\Mapping;


class CompositeDataElementMapping extends DataElementMapping
{
    /** @var  DataElementMapping[] */
    private $dataElements;

    /**
     * @return DataElementMapping[]
     */
    public function getDataElements()
    {
        return $this->dataElements;
    }

    /**
     * @param DataElementMapping[] $dataElements
     */
    public function setDataElements($dataElements)
    {
        $this->dataElements = $dataElements;
    }

    /**
     * @param DataElementMapping $dataElement
     * @return $this
     */
    public function addDataElement(DataElementMapping $dataElement)
    {
        $this->dataElements[] = $dataElement;
        return $this;
    }
}
