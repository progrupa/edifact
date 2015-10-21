<?php

namespace EDI\Mapping;

class DataElementMapping
{
    /** @var  int */
    private $id;
    /** @var bool  */
    private $required = false;

    public function __construct($id, $required = false)
    {
        $this->id = $id;
        $this->required = (bool) $required;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }
}
