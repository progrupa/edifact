<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class FII
{
    public static function create($partyQualifier, $accountNumber, $bankName, $accountHolderName = '', $currencyCoded = 'PLN', $country = null)
    {
        $segment = new Segment('FII');
        $segment->partyQualifier = $partyQualifier;
        $segment->accountIdentification = [
            'accountHolderNumber' => $accountNumber,
            'accountHolderName' => $accountHolderName,
            'currencyCoded' => $currencyCoded,
        ];
        $segment->institutionIdentification = [
            'institutionNameIdentification' => null,
            'codeListQualifier' => null,
            'codeListResponsibleAgencyCoded' => null,
            'institutionBranchNumber' => null,
            'institutionName' => $bankName,
            'institutionBranchPlace' => null,
        ];
        $segment->countryCoded = $country ? strtoupper($country) : null;

        return $segment;
    }
}
