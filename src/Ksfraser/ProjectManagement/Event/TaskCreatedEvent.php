<?php
/**
 * Task Created Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Task;

class TaskCreatedEvent extends TaskEvent
{
    public function __construct(Task $task)
    {
        parent::__construct($task);
    }
}