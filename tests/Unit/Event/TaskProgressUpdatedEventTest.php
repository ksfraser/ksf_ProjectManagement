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

class TaskProgressUpdatedEventTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        $this->task = new Task('1', 'proj1', 'Task', 'Desc');
    }

    public function testGetTask(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 0.0, 50.0);
        $this->assertSame($this->task, $event->getTask());
    }

    public function testGetPreviousProgress(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 25.0, 75.0);
        $this->assertSame(25.0, $event->getPreviousProgress());
    }

    public function testGetNewProgress(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 25.0, 75.0);
        $this->assertSame(75.0, $event->getNewProgress());
    }

    public function testGetProgressDelta(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 25.0, 75.0);
        $this->assertSame(50.0, $event->getProgressDelta());
    }

    public function testGetProgressDeltaNegative(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 75.0, 50.0);
        $this->assertSame(-25.0, $event->getProgressDelta());
    }

    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 0.0, 50.0);
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testStopPropagation(): void
    {
        $event = new TaskProgressUpdatedEvent($this->task, 0.0, 50.0);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}