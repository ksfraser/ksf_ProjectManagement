<?php
/**
 * Base Task Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Task;
use Psr\EventDispatcher\StoppableEventInterface;

abstract class TaskEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;
    private Task $task;

    public function __construct(
        Task $task
    ) {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}