<?php
declare(strict_types=1);

namespace Procurios\Meeting;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final class Meeting
{
    /** @var UuidInterface */
    private $meetingId;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var DateTimeImmutable */
    private $start;
    /** @var DateTimeImmutable */
    private $end;
    /** @var Program */
    private $program;

    /**
     * @param UuidInterface $meetingId
     * @param string $title
     * @param string $description
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @param Program $program
     * @throws \Exception
     */
    public function __construct(
        UuidInterface $meetingId,
        string $title,
        string $description,
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        Program $program
    ) {
        $this->meetingId = $meetingId;

        if (empty($title)) {
            throw new \Exception('Title cannot be empty');
        }

        if (strlen($title) < 5) {
            throw new \Exception('Title length must be at least five characters');
        }

        $this->title = $title;
        $this->description = $description;
        $this->start = $start;

        if ($end < $start) {
            throw new \Exception('End time must be after the start time');
        }

        $this->end = $end;

        if (count($program->getProgramSlots()) < 1) {
            throw new \Exception('Meeting program must have at least one program slot');
        }

        $this->program = $program;
    }

    public function reschedule(DateTimeImmutable $start) {
        $diff = $this->start->diff($start);

        $newStart = $this->start->add($diff);
        $newEnd = $this->end->add($diff);
        $this->start = $newStart;
        $this->end = $newEnd;

        $this->program->reschedule($diff);
    }

    public function getStart() : DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd() : DateTimeImmutable
    {
        return $this->end;
    }

    public function getProgram() : Program
    {
        return $this->program;
    }
}
