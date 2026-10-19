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

/**
 * @BABOK Related: FR-PM-002 — base PSR-14 event for a task aggregate change
 */
abstract class TaskEvent implements StoppableEventInterface
{
    /** @var bool */
    private $propagationStopped = false;
    /** @var Task */
    private $task;

    /**
     * @BABOK Related: FR-PM-002 — task event
     */
    public function __construct(
        Task $task
    ) {
        $this->task = $task;
    }

    /**
     * @BABOK Related: FR-PM-002 — task event
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @BABOK Related: PSR-14 plumbing; carries no PM requirement
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * @BABOK Related: PSR-14 plumbing; carries no PM requirement
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}