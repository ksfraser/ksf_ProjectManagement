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

/**
 * @BABOK Related: FR-PM-002 — tests the task.created event
 */
class TaskCreatedEventTest extends TestCase
{
    /** @var Task */
    private $task;

    /**
     * @BABOK Related: FR-PM-002 — task.created event payload
     */
    protected function setUp(): void
    {
        $this->task = new Task('1', 'proj1', 'Task', 'Desc');
    }

    /**
     * @BABOK Related: FR-PM-002 — task.created event payload
     */
    public function testGetTask(): void
    {
        $event = new TaskCreatedEvent($this->task);
        $this->assertSame($this->task, $event->getTask());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new TaskCreatedEvent($this->task);
        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testStopPropagation(): void
    {
        $event = new TaskCreatedEvent($this->task);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}