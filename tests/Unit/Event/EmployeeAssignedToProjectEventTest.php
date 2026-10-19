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

/**
 * @BABOK Related: FR-PM-003 — tests the employee.assigned_to_project event
 */
class EmployeeAssignedToProjectEventTest extends TestCase
{
    /** @var ProjectAssignment */
    private $assignment;

    /**
     * @BABOK Related: FR-PM-003 — allocation event payload
     */
    protected function setUp(): void
    {
        $this->assignment = new ProjectAssignment('proj1', 'emp1', 'Developer', new DateTime('2024-01-01'), 100.0);
    }

    /**
     * @BABOK Related: FR-PM-003 — allocation event payload
     */
    public function testGetAssignment(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame($this->assignment, $event->getAssignment());
    }

    /**
     * @BABOK Related: FR-PM-003 — allocation event payload
     */
    public function testGetProjectId(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame('proj1', $event->getProjectId());
    }

    /**
     * @BABOK Related: FR-PM-003 — allocation event payload
     */
    public function testGetEmployeeId(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame('emp1', $event->getEmployeeId());
    }

    /**
     * @BABOK Related: FR-PM-003 — allocation event payload
     */
    public function testGetRole(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertSame('Developer', $event->getRole());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testStopPropagation(): void
    {
        $event = new EmployeeAssignedToProjectEvent($this->assignment);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}