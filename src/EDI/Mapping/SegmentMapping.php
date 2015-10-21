<?php

namespace EDI\Mapping;


class SegmentMapping
{
    /** @var  string */
    private $id;
    /** @var  string */
    private $name;
    /** @var  string */
    private $desc;
    /** @var  DataElementMapping[] */
    private $dataElements;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

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
