<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class DTM
{
    public static function create($qualifier = 318 /* Request time */, \DateTime $date = null)
    {
        $segment = new Segment('DTM');
        $segment->datetimeperiod = [
            'datetimeperiodQualifier' => $qualifier,
            'datetimeperiod' => $date ? $date->format('YmdHis') : date('YmdHis'),
            'datetimeperiodFormatQualifier' => 204, //  CCYYMMDDHHMMSS
        ];

        return $segment;
    }
}
