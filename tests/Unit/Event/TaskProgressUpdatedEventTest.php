<?php
/**
 * TaskProgressUpdatedEvent Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Event;

use Ksfraser\ProjectManagement\Entity\Task;
use Ksfraser\ProjectManagement\Event\TaskProgressUpdatedEvent;
use PHPUnit\Framework\TestCase;

/**
 * @BABOK Related: FR-PM-004 — tests the task.progress_updated event
 */
class TaskProgressUpdatedEventTest extends TestCase
{
    /** @var Task */
    private $task;

    /**
     * @BABOK Related: FR-PM-004 — progress change event payload
     */
    protected function setUp(): void
    {
        $this->task = new Task('1', 'proj1', 'Task', 'Desc');
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event payload
     */
    public function testGetTask(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 0.0, 50.0);
        $this->assertSame($this->task, $event->getTask());
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event payload
     */
    public function testGetPreviousProgress(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 25.0, 75.0);
        $this->assertSame(25.0, $event->getPreviousProgress());
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event payload
     */
    public function testGetNewProgress(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 25.0, 75.0);
        $this->assertSame(75.0, $event->getNewProgress());
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event payload
     */
    public function testGetProgressDelta(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 25.0, 75.0);
        $this->assertSame(50.0, $event->getProgressDelta());
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event payload
     */
    public function testGetProgressDeltaNegative(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 75.0, 50.0);
        $this->assertSame(-25.0, $event->getProgressDelta());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 0.0, 50.0);
        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testStopPropagation(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 0.0, 50.0);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}