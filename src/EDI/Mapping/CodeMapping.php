<?php

namespace EDI\Mapping;


class CodeMapping
{
    /** @var string */
    private $id;
    /** @var string */
    private $desc;

    public function __construct($id, $desc)
    {
        $this->id = $id;
        $this->desc = $desc;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }
}
