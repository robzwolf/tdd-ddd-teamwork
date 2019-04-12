<?php
declare(strict_types=1);

namespace Procurios\Meeting\test;

use DateTimeImmutable;
use Procurios\Meeting\Meeting;
use Procurios\Meeting\Program;
use Procurios\Meeting\ProgramSlot;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class MeetingTest extends TestCase
{
    public function testThatValidMeetingsCanBeInstantiated()
    {
        $this->assertInstanceOf(Meeting::class, new Meeting(
            Uuid::uuid4(),
            'TDD, DDD & Teamwork',
            'This is a silly workshop, don\'t come',
            new DateTimeImmutable('2017-12-15 19:00'),
            new DateTimeImmutable('2017-12-15 21:00'),
            new Program([
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 19:00'),
                    new DateTimeImmutable('2017-12-15 20:00'),
                    'Divergence',
                    'Main room'
                ),
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 20:00'),
                    new DateTimeImmutable('2017-12-15 21:00'),
                    'Convergence',
                    'Main room'
                ),
            ])
        ));
    }

    public function testThatMeetingsHaveTitles()
    {
        $this->expectException(\Exception::class);

        $meeting = new Meeting(
            Uuid::uuid4(),
            '',
            'This meeting title is blank!',
            new DateTimeImmutable('2017-12-15 19:00'),
            new DateTimeImmutable('2017-12-15 21:00'),
            new Program([
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 19:00'),
                    new DateTimeImmutable('2017-12-15 20:00'),
                    'Divergence',
                    'Main room'
                ),
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 20:00'),
                    new DateTimeImmutable('2017-12-15 21:00'),
                    'Convergence',
                    'Main room'
                ),
            ])
        );
    }

    public function testThatMeetingTitlesAreAtLeastFiveCharactersLong()
    {
        $this->expectException(\Exception::class);

        $meeting = new Meeting(
            Uuid::uuid4(),
            'Yo',
            'This meeting title is just two characters long...',
            new DateTimeImmutable('2017-12-15 19:00'),
            new DateTimeImmutable('2017-12-15 21:00'),
            new Program([
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 19:00'),
                    new DateTimeImmutable('2017-12-15 20:00'),
                    'Divergence',
                    'Main room'
                ),
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 20:00'),
                    new DateTimeImmutable('2017-12-15 21:00'),
                    'Convergence',
                    'Main room'
                ),
            ])
        );
    }

    public function testThatMeetingEndTimeIsAfterStartTime()
    {
        $this->expectException(\Exception::class);

        $meeting = new Meeting(
            Uuid::uuid4(),
            'Yoyoyo',
            'This meeting title is just two characters long...',
            new DateTimeImmutable('2017-12-15 21:00'),
            new DateTimeImmutable('2017-12-15 19:00'),
            new Program([
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 19:00'),
                    new DateTimeImmutable('2017-12-15 20:00'),
                    'Divergence',
                    'Main room'
                ),
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 20:00'),
                    new DateTimeImmutable('2017-12-15 21:00'),
                    'Convergence',
                    'Main room'
                ),
            ])
        );
    }

    public function testThatMeetingsHaveAtLeastOneProgramSlot()
    {
        $this->expectException(\Exception::class);

        $meeting = new Meeting(
            Uuid::uuid4(),
            'Meeting with no program slots',
            'Hello world',
            new DateTimeImmutable('2017-12-15 19:00'),
            new DateTimeImmutable('2017-12-15 21:00'),
            new Program(
                []
            )
        );
    }

    public function testThatMeetingCanBeRescheduled()
    {
        $meetingId = Uuid::uuid4();

        $actualMeeting = new Meeting(
            $meetingId,
            'TDD, DDD & Teamwork',
            "This is a silly workshop, don't come",
            new DateTimeImmutable('2017-12-15 19:00'),
            new DateTimeImmutable('2017-12-15 21:00'),
            new Program([
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 19:00'),
                    new DateTimeImmutable('2017-12-15 20:00'),
                    'Divergence',
                    'Main room'
                ),
                new ProgramSlot(
                    new DateTimeImmutable('2017-12-15 20:00'),
                    new DateTimeImmutable('2017-12-15 21:00'),
                    'Convergence',
                    'Main room'
                ),
            ])
        );

        $newStart = new DateTimeImmutable('2018-12-15 19:00');
        $actualMeeting->reschedule($newStart);

        $expectedMeeting = new Meeting(
            $meetingId,
            'TDD, DDD & Teamwork',
            "This is a silly workshop, don't come",
            new DateTimeImmutable('2018-12-15 19:00'),
            new DateTimeImmutable('2018-12-15 21:00'),
            new Program([
                new ProgramSlot(
                    new DateTimeImmutable('2018-12-15 19:00'),
                    new DateTimeImmutable('2018-12-15 20:00'),
                    'Divergence',
                    'Main room'
                ),
                new ProgramSlot(
                    new DateTimeImmutable('2018-12-15 20:00'),
                    new DateTimeImmutable('2018-12-15 21:00'),
                    'Convergence',
                    'Main room'
                ),
            ])
        );

        $this->assertEquals($expectedMeeting, $actualMeeting);
        $this->assertNotSame($expectedMeeting, $actualMeeting);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function programSlotShouldOverlap()
    {
        $slot1 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 20:00'),
            new DateTimeImmutable('2018-12-15 21:00'),
            'Workshop 1',
            'Room 1'
        );

        $slot2 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 20:30'),
            new DateTimeImmutable('2018-12-15 21:30'),
            'Workshop 2',
            'Room 1'
        );

        $this->assertTrue($slot1->overlapsWith($slot2));
        $this->assertTrue($slot2->overlapsWith($slot1));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function programSlotShouldNotOverlap()
    {
        $slot1 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 20:00'),
            new DateTimeImmutable('2018-12-15 21:00'),
            'Workshop 1',
            'Room 1'
        );

        $slot2 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 21:30'),
            new DateTimeImmutable('2018-12-15 22:00'),
            'Workshop 2',
            'Room 1'
        );

        $this->assertFalse($slot1->overlapsWith($slot2));
        $this->assertFalse($slot2->overlapsWith($slot1));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function programSlotShouldOverlapWhenSlotIsWithinOtherSlot()
    {
        $slot1 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 20:00'),
            new DateTimeImmutable('2018-12-15 22:00'),
            'Workshop 1',
            'Room 1'
        );

        $slot2 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 20:30'),
            new DateTimeImmutable('2018-12-15 21:30'),
            'Workshop 2',
            'Room 1'
        );

        $this->assertTrue($slot1->overlapsWith($slot2));
        $this->assertTrue($slot2->overlapsWith($slot1));
    }

    /**
     * @test
     */
    public function identicalProgramSlotsShouldOverlap()
    {
        $slot1 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 20:00'),
            new DateTimeImmutable('2018-12-15 22:00'),
            'Workshop 1',
            'Room 1'
        );

        $slot2 = new ProgramSlot(
            new DateTimeImmutable('2018-12-15 20:00'),
            new DateTimeImmutable('2018-12-15 22:00'),
            'Workshop 2',
            'Room 1'
        );

        $this->assertTrue($slot1->overlapsWith($slot2));
        $this->assertTrue($slot2->overlapsWith($slot1));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function programSlotsShouldOverlap() {

        $this->expectException(\Exception::class);

        $program = new Program([
            new ProgramSlot(
                new DateTimeImmutable('2018-12-15 19:00'),
                new DateTimeImmutable('2018-12-15 20:30'),
                'Divergence',
                'Main room'
            ),
            new ProgramSlot(
                new DateTimeImmutable('2018-12-15 20:00'),
                new DateTimeImmutable('2018-12-15 21:00'),
                'Convergence',
                'Main room'
            ),
        ]);
    }

    /**
    * @test
    * @throws \Exception
    */
    public function noProgramSlotsShouldOverlap() {
        $program = new Program([
            new ProgramSlot(
                new DateTimeImmutable('2018-12-15 19:00'),
                new DateTimeImmutable('2018-12-15 20:00'),
                'Divergence',
                'Main room'
            ),
            new ProgramSlot(
                new DateTimeImmutable('2018-12-15 20:00'),
                new DateTimeImmutable('2018-12-15 21:00'),
                'Convergence',
                'Main room'
            ),
        ]);

        $this->assertInstanceOf(Program::class, $program);
    }
}
