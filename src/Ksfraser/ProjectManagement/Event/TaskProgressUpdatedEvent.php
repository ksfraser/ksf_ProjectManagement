<?php
/**
 * Task Progress Updated Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Task;

class TaskProgressUpdatedEvent extends TaskEvent
{
    public function __construct(
        Task $task,
        private readonly float $previousProgress,
        private readonly float $newProgress
    ) {
        parent::__construct($task);
    }

    public function getPreviousProgress(): float
    {
        return $this->previousProgress;
    }

    public function getNewProgress(): float
    {
        return $this->newProgress;
    }

    public function getProgressDelta(): float
    {
        return $this->newProgress - $this->previousProgress;
    }
}