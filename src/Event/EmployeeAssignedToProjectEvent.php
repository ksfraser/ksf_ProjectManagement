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

/**
 * @BABOK Related: FR-PM-003 — the employee.assigned_to_project integration event
 */
class EmployeeAssignedToProjectEvent implements StoppableEventInterface
{
    /** @var bool */
    private $propagationStopped = false;
    /** @var ProjectAssignment */
    private $assignment;

    /**
     * @BABOK Related: FR-PM-003 — emits on allocating an employee to a project
     */
    public function __construct(
        ProjectAssignment $assignment
    ) {
        $this->assignment = $assignment;
    }

    /**
     * @BABOK Related: FR-PM-003 — emits on allocating an employee to a project
     */
    public function getAssignment(): ProjectAssignment
    {
        return $this->assignment;
    }

    /**
     * @BABOK Related: FR-PM-003 — emits on allocating an employee to a project
     */
    public function getProjectId(): string
    {
        return $this->assignment->getProjectId();
    }

    /**
     * @BABOK Related: FR-PM-003 — emits on allocating an employee to a project
     */
    public function getEmployeeId(): string
    {
        return $this->assignment->getEmployeeId();
    }

    /**
     * @BABOK Related: FR-PM-003 — emits on allocating an employee to a project
     */
    public function getRole(): string
    {
        return $this->assignment->getRole();
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