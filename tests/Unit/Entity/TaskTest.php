<?php
/**
 * Task Entity Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Entity
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Entity;

use DateTime;
use Ksfraser\ProjectManagement\Entity\Task;
use PHPUnit\Framework\TestCase;

/**
 * @BABOK Related: FR-PM-002, FR-PM-004, FR-PM-005 — tests the Task entity
 */
class TaskTest extends TestCase
{
    /** @var Task */
    private $task;

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    protected function setUp(): void
    {
        $this->task = new Task('1', 'proj1', 'Test Task', 'Test Description', 'emp1');
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testConstructorSetsRequiredFields(): void
    {
        $this->assertSame('1', $this->task->getTaskId());
        $this->assertSame('proj1', $this->task->getProjectId());
        $this->assertSame('Test Task', $this->task->getName());
        $this->assertSame('Test Description', $this->task->getDescription());
        $this->assertSame('emp1', $this->task->getAssignedTo());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testConstructorSetsDefaultValues(): void
    {
        $this->assertSame('', $this->task->getParentTaskId());
        $this->assertNull($this->task->getStartDate());
        $this->assertNull($this->task->getEndDate());
        $this->assertSame(0.0, $this->task->getEstimatedHours());
        $this->assertSame(0.0, $this->task->getActualHours());
        $this->assertSame(0.0, $this->task->getProgress());
        $this->assertSame('Medium', $this->task->getPriority());
        $this->assertSame('Not Started', $this->task->getStatus());
    }

    /**
     * @BABOK Related: FR-PM-002 — parent-child hierarchy (BR-TASK-001)
     */
    public function testSetParentTaskIdReturnsSelf(): void
    {
        $result = $this->task->setParentTaskId('parent1');
        $this->assertSame($this->task, $result);
        $this->assertSame('parent1', $this->task->getParentTaskId());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testSetNameReturnsSelf(): void
    {
        $result = $this->task->setName('New Name');
        $this->assertSame($this->task, $result);
        $this->assertSame('New Name', $this->task->getName());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testSetDescriptionReturnsSelf(): void
    {
        $result = $this->task->setDescription('New Description');
        $this->assertSame($this->task, $result);
    }

    /**
     * @BABOK Related: FR-PM-002 — task assignment (BR-TASK-002)
     */
    public function testSetAssignedToReturnsSelf(): void
    {
        $result = $this->task->setAssignedTo('emp2');
        $this->assertSame($this->task, $result);
        $this->assertSame('emp2', $this->task->getAssignedTo());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testSetStartDateReturnsSelf(): void
    {
        $startDate = new DateTime('2024-01-01');
        $result = $this->task->setStartDate($startDate);
        $this->assertSame($this->task, $result);
        $this->assertSame($startDate, $this->task->getStartDate());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testSetStartDateCanBeNull(): void
    {
        $this->task->setStartDate(new DateTime('2024-01-01'));
        $this->task->setStartDate(null);
        $this->assertNull($this->task->getStartDate());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testSetEndDateReturnsSelf(): void
    {
        $endDate = new DateTime('2024-01-31');
        $result = $this->task->setEndDate($endDate);
        $this->assertSame($this->task, $result);
        $this->assertSame($endDate, $this->task->getEndDate());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testSetEndDateCanBeNull(): void
    {
        $this->task->setEndDate(new DateTime('2024-01-31'));
        $this->task->setEndDate(null);
        $this->assertNull($this->task->getEndDate());
    }

    /**
     * @BABOK Related: FR-PM-002 — estimates vs actuals (BR-TASK-004)
     */
    public function testSetEstimatedHoursReturnsSelf(): void
    {
        $result = $this->task->setEstimatedHours(40.0);
        $this->assertSame($this->task, $result);
        $this->assertSame(40.0, $this->task->getEstimatedHours());
    }

    /**
     * @BABOK Related: FR-PM-002 — estimates vs actuals (BR-TASK-004)
     */
    public function testSetActualHoursReturnsSelf(): void
    {
        $result = $this->task->setActualHours(20.0);
        $this->assertSame($this->task, $result);
        $this->assertSame(20.0, $this->task->getActualHours());
    }

    /**
     * @BABOK Related: FR-PM-004 — progress is constrained to 0-100%
     */
    public function testSetProgressClampsToValidRange(): void
    {
        $this->task->setProgress(-10.0);
        $this->assertSame(0.0, $this->task->getProgress());

        $this->task->setProgress(150.0);
        $this->assertSame(100.0, $this->task->getProgress());

        $this->task->setProgress(50.0);
        $this->assertSame(50.0, $this->task->getProgress());
    }

    /**
     * @BABOK Related: FR-PM-004 — track progress
     */
    public function testSetProgressReturnsSelf(): void
    {
        $result = $this->task->setProgress(75.0);
        $this->assertSame($this->task, $result);
    }

    /**
     * @BABOK Related: FR-PM-002 — priority assignment (BR-TASK-006)
     */
    public function testSetPriorityReturnsSelf(): void
    {
        $result = $this->task->setPriority('High');
        $this->assertSame($this->task, $result);
        $this->assertSame('High', $this->task->getPriority());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testSetStatusReturnsSelf(): void
    {
        $result = $this->task->setStatus('In Progress');
        $this->assertSame($this->task, $result);
        $this->assertSame('In Progress', $this->task->getStatus());
    }

    /**
     * @BABOK Related: FR-PM-004 — completion test
     */
    public function testIsCompletedReturnsTrueWhenStatusCompleted(): void
    {
        $this->task->setStatus('Completed');
        $this->assertTrue($this->task->isCompleted());
    }

    /**
     * @BABOK Related: FR-PM-004 — completion test
     */
    public function testIsCompletedReturnsTrueWhenProgress100(): void
    {
        $this->task->setProgress(100.0);
        $this->assertTrue($this->task->isCompleted());
    }

    /**
     * @BABOK Related: FR-PM-004 — completion test
     */
    public function testIsCompletedReturnsFalseOtherwise(): void
    {
        $this->task->setStatus('In Progress');
        $this->task->setProgress(50.0);
        $this->assertFalse($this->task->isCompleted());
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function testIsOverdueReturnsFalseWhenNoEndDate(): void
    {
        $this->assertFalse($this->task->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-005 — a completed task is never overdue
     */
    public function testIsOverdueReturnsFalseWhenCompleted(): void
    {
        $pastDate = new DateTime('2020-01-01');
        $this->task->setEndDate($pastDate);
        $this->task->setStatus('Completed');
        $this->assertFalse($this->task->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function testIsOverdueReturnsTrueWhenPastEndDateAndNotCompleted(): void
    {
        $pastDate = new DateTime('2020-01-01');
        $this->task->setEndDate($pastDate);
        $this->task->setStatus('In Progress');
        $this->assertTrue($this->task->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function testIsOverdueReturnsFalseWhenFutureEndDate(): void
    {
        $futureDate = new DateTime('2030-01-01');
        $this->task->setEndDate($futureDate);
        $this->assertFalse($this->task->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — duration calculation
     */
    public function testGetDurationReturnsNullWhenNoStartDate(): void
    {
        $this->task->setEndDate(new DateTime('2024-01-31'));
        $this->assertNull($this->task->getDuration());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — duration calculation
     */
    public function testGetDurationReturnsNullWhenNoEndDate(): void
    {
        $this->task->setStartDate(new DateTime('2024-01-01'));
        $this->assertNull($this->task->getDuration());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — duration calculation
     */
    public function testGetDurationReturnsDaysDifference(): void
    {
        $this->task->setStartDate(new DateTime('2024-01-01'));
        $this->task->setEndDate(new DateTime('2024-01-11'));
        $this->assertSame(10, $this->task->getDuration());
    }

    /**
     * @BABOK Related: FR-PM-002 — parent-child hierarchy (BR-TASK-001)
     */
    public function testHasSubtasksReturnsFalse(): void
    {
        $this->assertFalse($this->task->hasSubtasks());
    }

    /**
     * @BABOK Related: FR-PM-002 — task entity behaviour
     */
    public function testToArrayReturnsAllFields(): void
    {
        $this->task->setParentTaskId('parent1');
        $this->task->setStartDate(new DateTime('2024-01-01'));
        $this->task->setEndDate(new DateTime('2024-01-31'));
        $this->task->setEstimatedHours(40.0);
        $this->task->setActualHours(20.0);
        $this->task->setProgress(50.0);
        $this->task->setPriority('High');
        $this->task->setStatus('In Progress');

        $array = $this->task->toArray();

        $this->assertSame('1', $array['task_id']);
        $this->assertSame('proj1', $array['project_id']);
        $this->assertSame('parent1', $array['parent_task_id']);
        $this->assertSame('Test Task', $array['name']);
        $this->assertSame('Test Description', $array['description']);
        $this->assertSame('emp1', $array['assigned_to']);
        $this->assertNotEmpty($array['start_date']);
        $this->assertNotEmpty($array['end_date']);
        $this->assertSame(40.0, $array['estimated_hours']);
        $this->assertSame(20.0, $array['actual_hours']);
        $this->assertSame(50.0, $array['progress']);
        $this->assertSame('High', $array['priority']);
        $this->assertSame('In Progress', $array['status']);
    }
}