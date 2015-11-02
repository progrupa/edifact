<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class MOA
{
    public static function create($amount, $type)
    {
        $segment = new Segment('MOA');
        $segment->monetaryAmount = ['monetaryAmountTypeQualifier' => $type, 'monetaryAmount' => $amount];

        return $segment;
    }
}
