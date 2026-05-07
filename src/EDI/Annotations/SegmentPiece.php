<?php

namespace EDI\Annotations;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class SegmentPiece
{
    public function __construct(
        public readonly int|string $position,
        public readonly ?array $parts = null,
    ) {}
}
