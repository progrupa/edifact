<?php

namespace EDI;

class Encoder
{
    private $messageMappings;
    private $segmentMappings;

    public function encode($interchange)
    {

    }

    /**
     * @return mixed
     */
    public function getMessageMappings()
    {
        return $this->messageMappings;
    }

    /**
     * @param mixed $messageMappings
     */
    public function setMessageMappings($messageMappings)
    {
        $this->messageMappings = $messageMappings;
    }

    /**
     * @return mixed
     */
    public function getSegmentMappings()
    {
        return $this->segmentMappings;
    }

    /**
     * @param mixed $segmentMappings
     */
    public function setSegmentMappings($segmentMappings)
    {
        $this->segmentMappings = $segmentMappings;
    }


}
