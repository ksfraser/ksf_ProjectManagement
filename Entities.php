<?php
/**
 * FrontAccounting Project Management Entities
 *
 * Data models for project management functionality.
 *
 * @package FA\Modules\ProjectManagement
 * @version 1.0.0
 * @author FrontAccounting Team
 * @license GPL-3.0
 */

namespace FA\Modules\ProjectManagement;

/**
 * Project Entity
 *
 * Represents a project with tasks, team members, and progress tracking
 */
class Project
{
    private string $projectId;
    private string $name;
    private string $description;
    private \DateTime $startDate;
    private ?\DateTime $endDate = null;
    private float $budget = 0.0;
    private string $customerId = '';
    private string $projectManager;
    private string $priority = 'Medium';
    private string $status = 'Planning';

    public function __construct(
        string $projectId,
        string $name,
        string $description,
        \DateTime $startDate,
        string $projectManager
    ) {
        $this->projectId = $projectId;
        $this->name = $name;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->projectManager = $projectManager;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): void
    {
        $this->budget = $budget;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getProjectManager(): string
    {
        return $this->projectManager;
    }

    public function setProjectManager(string $projectManager): void
    {
        $this->projectManager = $projectManager;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get project duration in days
     *
     * @return int|null
     */
    public function getDuration(): ?int
    {
        if (!$this->endDate) {
            return null;
        }

        return $this->startDate->diff($this->endDate)->days;
    }

    /**
     * Check if project is overdue
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        if (!$this->endDate) {
            return false;
        }

        return $this->endDate < new \DateTime() && $this->status !== 'Completed';
    }
}

/**
 * Task Entity
 *
 * Represents a project task with assignments and progress tracking
 */
class Task
{
    private string $taskId;
    private string $projectId;
    private string $parentTaskId = '';
    private string $name;
    private string $description;
    private string $assignedTo = '';
    private ?\DateTime $startDate = null;
    private ?\DateTime $endDate = null;
    private float $estimatedHours = 0.0;
    private float $actualHours = 0.0;
    private float $progress = 0.0;
    private string $priority = 'Medium';
    private string $status = 'Not Started';

    public function __construct(
        string $taskId,
        string $projectId,
        string $name,
        string $description,
        string $assignedTo = ''
    ) {
        $this->taskId = $taskId;
        $this->projectId = $projectId;
        $this->name = $name;
        $this->description = $description;
        $this->assignedTo = $assignedTo;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getParentTaskId(): string
    {
        return $this->parentTaskId;
    }

    public function setParentTaskId(string $parentTaskId): void
    {
        $this->parentTaskId = $parentTaskId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(string $assignedTo): void
    {
        $this->assignedTo = $assignedTo;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getEstimatedHours(): float
    {
        return $this->estimatedHours;
    }

    public function setEstimatedHours(float $estimatedHours): void
    {
        $this->estimatedHours = $estimatedHours;
    }

    public function getActualHours(): float
    {
        return $this->actualHours;
    }

    public function setActualHours(float $actualHours): void
    {
        $this->actualHours = $actualHours;
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    public function setProgress(float $progress): void
    {
        $this->progress = max(0, min(100, $progress));
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Check if task is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Completed' || $this->progress >= 100;
    }

    /**
     * Check if task is overdue
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        if (!$this->endDate) {
            return false;
        }

        return $this->endDate < new \DateTime() && !$this->isCompleted();
    }

    /**
     * Get task duration in days
     *
     * @return int|null
     */
    public function getDuration(): ?int
    {
        if (!$this->startDate || !$this->endDate) {
            return null;
        }

        return $this->startDate->diff($this->endDate)->days;
    }
}

/**
 * Project Assignment Entity
 *
 * Represents employee assignment to a project
 */
class ProjectAssignment
{
    private string $projectId;
    private string $employeeId;
    private string $role;
    private \DateTime $startDate;
    private ?\DateTime $endDate = null;
    private float $allocationPercentage = 100.0;

    public function __construct(
        string $projectId,
        string $employeeId,
        string $role,
        \DateTime $startDate,
        float $allocationPercentage = 100.0
    ) {
        $this->projectId = $projectId;
        $this->employeeId = $employeeId;
        $this->role = $role;
        $this->startDate = $startDate;
        $this->allocationPercentage = $allocationPercentage;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getAllocationPercentage(): float
    {
        return $this->allocationPercentage;
    }

    public function setAllocationPercentage(float $allocationPercentage): void
    {
        $this->allocationPercentage = max(0, min(100, $allocationPercentage));
    }

    /**
     * Check if assignment is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = new \DateTime();
        return $this->startDate <= $now && ($this->endDate === null || $this->endDate >= $now);
    }
}