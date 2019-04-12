<?php
declare(strict_types=1);

namespace Procurios\Meeting;

use DateTimeImmutable;

final class ProgramSlot
{
    /** @var DateTimeImmutable */
    private $start;
    /** @var DateTimeImmutable */
    private $end;
    /** @var string */
    private $title;
    /** @var string */
    private $room;

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @param string $title
     * @param string $room
     */
    public function __construct(DateTimeImmutable $start, DateTimeImmutable $end, string $title, string $room)
    {
        $this->start = $start;
        $this->end = $end;
        $this->title = $title;
        $this->room = $room;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEnd(): DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @param DateTimeImmutable $newStart
     */
    public function setStart(DateTimeImmutable $newStart)
    {
        $this->start = $newStart;
    }

    /**
     * @param DateTimeImmutable $newEnd
     */
    public function setEnd(DateTimeImmutable $newEnd)
    {
        $this->end = $newEnd;
    }

    public function overlapsWith(ProgramSlot $slot2)
    {
        if ($slot2->start < $this->end
            && $slot2->end > $this->start) {
            return true;
        }

        return false;
    }
}
