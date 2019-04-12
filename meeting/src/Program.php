<?php
declare(strict_types=1);

namespace Procurios\Meeting;

use DateInterval;
use Webmozart\Assert\Assert;

final class Program
{
    /** @var ProgramSlot[] */
    private $programSlots;

    /**
     * @param ProgramSlot[] $programSlots
     */
    public function __construct(array $programSlots)
    {
        Assert::allIsInstanceOf($programSlots, ProgramSlot::class);
        $this->programSlots = $programSlots;
        $this->checkForOverlaps();
    }

    /**
     * Get the program slots.
     * @return array|ProgramSlot[]
     */
    public function getProgramSlots()
    {
        return $this->programSlots;
    }

    public function reschedule(DateInterval $diff) {
        foreach ($this->programSlots as $programSlot) {
            $newStart = $programSlot->getStart()->add($diff);
            $newEnd = $programSlot->getEnd()->add($diff);

            $programSlot->setStart($newStart);
            $programSlot->setEnd($newEnd);
        }
    }

    public function checkForOverlaps()
    {
        foreach ($this->programSlots as $programSlot) {
            foreach ($this->programSlots as $slot) {
                if ($programSlot == $slot) {
                    continue;
                }
                if ($programSlot->overlapsWith($slot)){
                    throw new \Exception('Times should not overlap');
                }
            }
        }
    }
}
