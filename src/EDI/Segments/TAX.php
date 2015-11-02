<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class TAX
{
    public static function create($rate, $taxType = 'VAT', $qualifier = 7 /* tax */)
    {
        $segment = new Segment('TAX');
        $segment->dutytaxfeeFunctionQualifier = $qualifier;
        $segment->dutytaxfeeType = ['dutytaxfeeTypeCoded' => $taxType];
        $segment->dutytaxfeeDetail = ['dutytaxfeeRate' => $rate];

        return $segment;
    }
}
