<?php
/**
 * Task Entity
 *
 * Represents a project task with assignments and progress tracking
 *
 * @package Ksfraser\ProjectManagement\Entity
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Entity;

use DateTime;
use DateTimeInterface;

class Task
{
    private string $taskId;
    private string $projectId;
    private string $parentTaskId;
    private string $name;
    private string $description;
    private string $assignedTo;
    private ?DateTime $startDate;
    private ?DateTime $endDate;
    private float $estimatedHours;
    private float $actualHours;
    private float $progress;
    private string $priority;
    private string $status;

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
        $this->parentTaskId = '';
        $this->startDate = null;
        $this->endDate = null;
        $this->estimatedHours = 0.0;
        $this->actualHours = 0.0;
        $this->progress = 0.0;
        $this->priority = 'Medium';
        $this->status = 'Not Started';
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

    public function setParentTaskId(string $parentTaskId): self
    {
        $this->parentTaskId = $parentTaskId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(string $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getEstimatedHours(): float
    {
        return $this->estimatedHours;
    }

    public function setEstimatedHours(float $estimatedHours): self
    {
        $this->estimatedHours = $estimatedHours;
        return $this;
    }

    public function getActualHours(): float
    {
        return $this->actualHours;
    }

    public function setActualHours(float $actualHours): self
    {
        $this->actualHours = $actualHours;
        return $this;
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    public function setProgress(float $progress): self
    {
        $this->progress = max(0.0, min(100.0, $progress));
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed' || $this->progress >= 100.0;
    }

    public function isOverdue(): bool
    {
        if ($this->endDate === null) {
            return false;
        }
        return $this->endDate < new DateTime() && !$this->isCompleted();
    }

    public function getDuration(): ?int
    {
        if ($this->startDate === null || $this->endDate === null) {
            return null;
        }
        return $this->startDate->diff($this->endDate)->days;
    }

    public function hasSubtasks(): bool
    {
        return false;
    }

    public function toArray(): array
    {
        return [
            'task_id' => $this->taskId,
            'project_id' => $this->projectId,
            'parent_task_id' => $this->parentTaskId,
            'name' => $this->name,
            'description' => $this->description,
            'assigned_to' => $this->assignedTo,
            'start_date' => $this->startDate?->format(DateTimeInterface::ATOM),
            'end_date' => $this->endDate?->format(DateTimeInterface::ATOM),
            'estimated_hours' => $this->estimatedHours,
            'actual_hours' => $this->actualHours,
            'progress' => $this->progress,
            'priority' => $this->priority,
            'status' => $this->status,
        ];
    }
}