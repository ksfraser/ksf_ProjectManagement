<?php
/**
 * TaskCreatedEvent Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Event;

use Ksfraser\ProjectManagement\Entity\Task;
use Ksfraser\ProjectManagement\Event\TaskCreatedEvent;
use PHPUnit\Framework\TestCase;

class TaskCreatedEventTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        $this->task = new Task('1', 'proj1', 'Task', 'Desc');
    }

    public function testGetTask(): void
    {
        $event = new TaskCreatedEvent($this->task);
        $this->assertSame($this->task, $event->getTask());
    }

    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new TaskCreatedEvent($this->task);
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testStopPropagation(): void
    {
        $event = new TaskCreatedEvent($this->task);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}