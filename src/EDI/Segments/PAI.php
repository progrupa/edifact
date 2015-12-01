<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class PAI
{
    public static function create()
    {
        $segment = new Segment('PAI');
        $segment->paymentInstructionDetails = [
            'paymentConditionsCoded' => 7, /* bank transfer */
            'paymentGuaranteeCoded' => 10, /* bank guarantee */
            'paymentMeansCoded' => 31,  /* debit transfer */
            'codeListQualifier' => null,
            'codeListResponsibleAgencyCoded' => null,
            'paymentChannelCoded' => 5, /* SWIFT */
        ];

        return $segment;
    }
}
