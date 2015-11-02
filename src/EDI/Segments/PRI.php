<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class PRI
{
    public static function create($price)
    {
        $segment = new Segment('PRI');
        $segment->priceInformation = ['priceQualifier' => 'AAA' /* net price */, 'price' => $price];

        return $segment;
    }
}
