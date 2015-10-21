<?php

namespace EDI\Annotations;


use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
final class SegmentPiece
{
    public $position;
    public $parts;
}
