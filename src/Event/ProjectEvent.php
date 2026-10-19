<?php
/**
 * Base Project Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Project;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * @BABOK Related: FR-PM-001 — base PSR-14 event for a project aggregate change
 */
abstract class ProjectEvent implements StoppableEventInterface
{
    /** @var bool */
    private $propagationStopped = false;
    /** @var Project */
    private $project;

    /**
     * @BABOK Related: FR-PM-001 — project event
     */
    public function __construct(
        Project $project
    ) {
        $this->project = $project;
    }

    /**
     * @BABOK Related: FR-PM-001 — project event
     */
    public function getProject(): Project
    {
        return $this->project;
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