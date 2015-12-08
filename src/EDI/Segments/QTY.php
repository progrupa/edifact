<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class QTY
{
    const DISCRETE = 1;
    const ACTUAL_STOCK = 145;

    public static function create($quantity, $quantitySpecifier = self::DISCRETE, $measureUnit = null)
    {
        $segment = new Segment('QTY');
        $segment->quantityDetails = [
            'quantityQualifier' => $quantitySpecifier,
            'quantity' => $quantity,
            'measureUnitQualifier' => $measureUnit,
        ];

        return $segment;
    }

}
