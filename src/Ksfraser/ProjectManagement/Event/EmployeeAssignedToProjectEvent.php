<?php
/**
 * Employee Assigned to Project Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use Psr\EventDispatcher\StoppableEventInterface;

class EmployeeAssignedToProjectEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(
        private readonly ProjectAssignment $assignment
    ) {
    }

    public function getAssignment(): ProjectAssignment
    {
        return $this->assignment;
    }

    public function getProjectId(): string
    {
        return $this->assignment->getProjectId();
    }

    public function getEmployeeId(): string
    {
        return $this->assignment->getEmployeeId();
    }

    public function getRole(): string
    {
        return $this->assignment->getRole();
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