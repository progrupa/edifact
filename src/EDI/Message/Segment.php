<?php

namespace EDI\Message;


class Segment
{
    /** @var string */
    private $code;
    /** @var array  */
    private $data = array();
    /** @var array  */
    private $rawData = array();

    public function __construct($code = null)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function get($name)
    {
        return $this->data[$name];
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param mixed $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    public function count()
    {
        return 1;
    }
}
