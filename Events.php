<?php
/**
 * FrontAccounting Project Management Events
 *
 * Event classes for project management functionality.
 *
 * @package FA\Modules\ProjectManagement
 * @version 1.0.0
 * @author FrontAccounting Team
 * @license GPL-3.0
 */

namespace FA\Modules\ProjectManagement;

/**
 * Base Project Event
 */
abstract class ProjectEvent
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}

/**
 * Project Created Event
 */
class ProjectCreatedEvent extends ProjectEvent
{
    // Event data available through parent
}

/**
 * Base Task Event
 */
abstract class TaskEvent
{
    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}

/**
 * Task Created Event
 */
class TaskCreatedEvent extends TaskEvent
{
    // Event data available through parent
}

/**
 * Task Progress Updated Event
 */
class TaskProgressUpdatedEvent extends TaskEvent
{
    // Event data available through parent
}

/**
 * Employee Assigned to Project Event
 */
class EmployeeAssignedToProjectEvent
{
    private ProjectAssignment $assignment;

    public function __construct(ProjectAssignment $assignment)
    {
        $this->assignment = $assignment;
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
}