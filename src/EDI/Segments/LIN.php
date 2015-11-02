<?php

namespace EDI\Segments;

use EDI\Message\Segment;

class LIN
{
    public static function create($lineNo, $itemId)
    {
        $segment = new Segment('LIN');
        $segment->lineItemNumber = $lineNo;
        $segment->itemNumberIdentification = ['itemNumber' => $itemId, 'itemNumberTypeCoded' => 'VN'];

        return $segment;
    }
}
