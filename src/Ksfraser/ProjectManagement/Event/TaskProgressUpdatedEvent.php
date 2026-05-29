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
    private float $previousProgress;
    private float $newProgress;

    public function __construct(
        Task $task,
        float $previousProgress,
        float $newProgress
    ) {
        $this->previousProgress = $previousProgress;
        $this->newProgress = $newProgress;
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