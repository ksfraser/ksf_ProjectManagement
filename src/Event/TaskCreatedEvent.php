<?php
/**
 * Task Created Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Task;

/**
 * @BABOK Related: FR-PM-002 — the task.created integration event
 */
class TaskCreatedEvent extends TaskEvent
{
    /**
     * @BABOK Related: FR-PM-002 — emits task.created on a new task
     */
    public function __construct(Task $task)
    {
        parent::__construct($task);
    }
}