<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class UNS
{
    public static function create($id)
    {
        $segment = new Segment('UNS');
        $segment->id = $id;
        return $segment;
    }

}
