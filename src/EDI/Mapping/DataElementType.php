<?php

namespace EDI\Mapping;


class DataElementType
{
    const A = 'a';  //  Any letters, special characters, and control characters. No digits.
    const AN = 'an'; // Any letters, digits, special characters, and control characters.
    const ID = 'id'; // Alphabetic, numeric, or alphanumeric identifier
    const N = 'n'; //   Numeric. Can include decimal mark (either point or comma is acceptable for this)

    const COMPOSITE = 'composite';
}
