<?php

namespace EDI\Segments;

use EDI\Message\Segment;

class LIN
{
    const ADDED = 1;
    const DELETED = 2;
    const CHANGED = 3;
    const NO_ACTION = 4;

    public static function create($lineNo, $itemId)
    {
        $segment = new Segment('LIN');
        $segment->lineItemNumber = $lineNo;
        $segment->itemNumberIdentification = ['itemNumber' => $itemId, 'itemNumberTypeCoded' => 'VN'];

        return $segment;
    }
}
