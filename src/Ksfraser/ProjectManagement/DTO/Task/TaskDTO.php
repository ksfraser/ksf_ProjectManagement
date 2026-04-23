<?php
/**
 * TaskDTO - Data Transfer Object for API/UI layer
 *
 * @package Ksfraser\ProjectManagement\DTO\Task
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DTO\Task;

class TaskDTO
{
    public function __construct(
        private readonly ?string $taskId = null,
        private readonly string $projectId = '',
        private readonly string $parentTaskId = '',
        private readonly string $name = '',
        private readonly string $description = '',
        private readonly string $assignedTo = '',
        private readonly ?string $assignedToName = null,
        private readonly string $startDate = '',
        private readonly ?string $endDate = null,
        private readonly float $estimatedHours = 0.0,
        private readonly float $actualHours = 0.0,
        private readonly float $progress = 0.0,
        private readonly string $priority = 'Medium',
        private readonly string $status = 'Not Started',
        private readonly array $subtasks = [],
        private readonly array $files = [],
        private readonly array $timeEntries = [],
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null,
        private readonly bool $inactive = false
    ) {
    }

    public function getTaskId(): ?string
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    public function getAssignedToName(): ?string
    {
        return $this->assignedToName;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getEstimatedHours(): float
    {
        return $this->estimatedHours;
    }

    public function getActualHours(): float
    {
        return $this->actualHours;
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubtasks(): array
    {
        return $this->subtasks;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getTimeEntries(): array
    {
        return $this->timeEntries;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function isInactive(): bool
    {
        return $this->inactive;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed' || $this->progress >= 100.0;
    }

    public function isOverdue(): bool
    {
        if (empty($this->endDate)) {
            return false;
        }
        return !$this->isCompleted() && $this->endDate < date('Y-m-d');
    }

    public function hasSubtasks(): bool
    {
        return !empty($this->subtasks);
    }

    public function getRemainingHours(): float
    {
        return max(0.0, $this->estimatedHours - $this->actualHours);
    }

    public function getCompletionRatio(): float
    {
        if ($this->estimatedHours <= 0) {
            return 0.0;
        }
        return min(100.0, ($this->actualHours / $this->estimatedHours) * 100.0);
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
            'assigned_to_name' => $this->assignedToName,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'estimated_hours' => $this->estimatedHours,
            'actual_hours' => $this->actualHours,
            'progress' => $this->progress,
            'priority' => $this->priority,
            'status' => $this->status,
            'subtasks' => $this->subtasks,
            'files' => $this->files,
            'time_entries' => $this->timeEntries,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'inactive' => $this->inactive,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            taskId: $data['task_id'] ?? null,
            projectId: $data['project_id'] ?? '',
            parentTaskId: $data['parent_task_id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            assignedTo: $data['assigned_to'] ?? '',
            assignedToName: $data['assigned_to_name'] ?? null,
            startDate: $data['start_date'] ?? '',
            endDate: $data['end_date'] ?? null,
            estimatedHours: (float) ($data['estimated_hours'] ?? 0.0),
            actualHours: (float) ($data['actual_hours'] ?? 0.0),
            progress: (float) ($data['progress'] ?? 0.0),
            priority: $data['priority'] ?? 'Medium',
            status: $data['status'] ?? 'Not Started',
            subtasks: $data['subtasks'] ?? [],
            files: $data['files'] ?? [],
            timeEntries: $data['time_entries'] ?? [],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            inactive: (bool) ($data['inactive'] ?? false)
        );
    }

    public static function fromEntity(\Ksfraser\ProjectManagement\Entity\Task $entity): self
    {
        return new self(
            taskId: $entity->getTaskId(),
            projectId: $entity->getProjectId(),
            parentTaskId: $entity->getParentTaskId(),
            name: $entity->getName(),
            description: $entity->getDescription(),
            assignedTo: $entity->getAssignedTo(),
            startDate: $entity->getStartDate()?->format('Y-m-d') ?? '',
            endDate: $entity->getEndDate()?->format('Y-m-d'),
            estimatedHours: $entity->getEstimatedHours(),
            actualHours: $entity->getActualHours(),
            progress: $entity->getProgress(),
            priority: $entity->getPriority(),
            status: $entity->getStatus()
        );
    }
}