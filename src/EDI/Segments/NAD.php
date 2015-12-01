<?php

namespace EDI\Segments;


use EDI\Message\Segment;

class NAD
{
    public static function create($partyQualifier, $partyId, $name, $address, $city, $postalCode, $country = null)
    {
        $segment = new Segment('NAD');
        $segment->partyQualifier = $partyQualifier;
        $segment->partyIdentificationDetails = [
            'partyIdentificationDetails' => $partyId,
            'codeListQualifier' => null,
            'codeListResponsibleAgencyCoded' => null,
        ];
        $segment->partyName = [
            0 => $name,
        ];
        $segment->street = [
            0 => $address,
        ];
        $segment->cityName = $city;
        $segment->postcodeIdentification = $postalCode;
        $segment->countryCoded = $country ? strtoupper($country) : null;

        return $segment;
    }

}
