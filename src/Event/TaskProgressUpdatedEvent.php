<?php
/**
 * Task Progress Updated Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Task;

/**
 * @BABOK Related: FR-PM-004 — the task.progress_updated integration event
 */
class TaskProgressUpdatedEvent extends TaskEvent
{
    /** @var float */
    private $previousProgress;
    /** @var float */
    private $newProgress;

    /**
     * @BABOK Related: FR-PM-004 — progress change event
     */
    public function __construct(
        Task $task,
        float $previousProgress,
        float $newProgress
    ) {
        $this->previousProgress = $previousProgress;
        $this->newProgress = $newProgress;
        parent::__construct($task);
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event
     */
    public function getPreviousProgress(): float
    {
        return $this->previousProgress;
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event
     */
    public function getNewProgress(): float
    {
        return $this->newProgress;
    }

    /**
     * @BABOK Related: FR-PM-004 — progress change event
     */
    public function getProgressDelta(): float
    {
        return $this->newProgress - $this->previousProgress;
    }
}