<?php
/**
 * EmployeeAssignedToProjectEvent Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Event;

use DateTime;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use Ksfraser\ProjectManagement\Event\EmployeeAssignedToProjectEvent;
use PHPUnit\Framework\TestCase;

class EmployeeAssignedToProjectEventTest extends TestCase
{
    private ProjectAssignment $assignment;

    protected function setUp(): void
    {
        $this->assignment = new ProjectAssignment('proj1', 'emp1', 'Developer', new DateTime('2024-01-01'), 100.0);
    }

    public function testGetAssignment(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame($this->assignment, $event->getAssignment());
    }

    public function testGetProjectId(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame('proj1', $event->getProjectId());
    }

    public function testGetEmployeeId(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame('emp1', $event->getEmployeeId());
    }

    public function testGetRole(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame('Developer', $event->getRole());
    }

    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testStopPropagation(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}