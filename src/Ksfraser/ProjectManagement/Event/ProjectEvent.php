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

abstract class ProjectEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(
        private readonly Project $project
    ) {
    }

    public function getProject(): Project
    {
        return $this->project;
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