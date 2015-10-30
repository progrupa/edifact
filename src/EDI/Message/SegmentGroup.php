<?php

namespace EDI\Message;


class SegmentGroup extends Segment
{
    use SegmentContainer;

    public function count()
    {
        $total = 0;
        foreach ($this->getSegments() as $seg) {
            $total += $seg->count();
        }

        return $total;
    }
}
