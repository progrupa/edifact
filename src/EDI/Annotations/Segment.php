<?php

namespace EDI\Annotations;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Segment
{
    public function __construct(
        public readonly string $value,
    ) {}
}
