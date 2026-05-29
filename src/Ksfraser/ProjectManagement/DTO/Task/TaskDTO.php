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
    private ?string $taskId = null;
    private string $projectId = '';
    private string $parentTaskId = '';
    private string $name = '';
    private string $description = '';
    private string $assignedTo = '';
    private ?string $assignedToName = null;
    private string $startDate = '';
    private ?string $endDate = null;
    private float $estimatedHours = 0.0;
    private float $actualHours = 0.0;
    private float $progress = 0.0;
    private string $priority = 'Medium';
    private string $status = 'Not Started';
    private array $subtasks = [];
    private array $files = [];
    private array $timeEntries = [];
    private ?string $createdAt = null;
    private ?string $updatedAt = null;
    private bool $inactive = false;

    public function __construct(
        ?string $taskId = null,
        string $projectId = '',
        string $parentTaskId = '',
        string $name = '',
        string $description = '',
        string $assignedTo = '',
        ?string $assignedToName = null,
        string $startDate = '',
        ?string $endDate = null,
        float $estimatedHours = 0.0,
        float $actualHours = 0.0,
        float $progress = 0.0,
        string $priority = 'Medium',
        string $status = 'Not Started',
        array $subtasks = [],
        array $files = [],
        array $timeEntries = [],
        ?string $createdAt = null,
        ?string $updatedAt = null,
        bool $inactive = false
    ) {
        $this->taskId = $taskId;
        $this->projectId = $projectId;
        $this->parentTaskId = $parentTaskId;
        $this->name = $name;
        $this->description = $description;
        $this->assignedTo = $assignedTo;
        $this->assignedToName = $assignedToName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->estimatedHours = $estimatedHours;
        $this->actualHours = $actualHours;
        $this->progress = $progress;
        $this->priority = $priority;
        $this->status = $status;
        $this->subtasks = $subtasks;
        $this->files = $files;
        $this->timeEntries = $timeEntries;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->inactive = $inactive;
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
            $data['task_id'] ?? null,
            $data['project_id'] ?? '',
            $data['parent_task_id'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['assigned_to'] ?? '',
            $data['assigned_to_name'] ?? null,
            $data['start_date'] ?? '',
            $data['end_date'] ?? null,
            (float) ($data['estimated_hours'] ?? 0.0),
            (float) ($data['actual_hours'] ?? 0.0),
            (float) ($data['progress'] ?? 0.0),
            $data['priority'] ?? 'Medium',
            $data['status'] ?? 'Not Started',
            $data['subtasks'] ?? [],
            $data['files'] ?? [],
            $data['time_entries'] ?? [],
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
            (bool) ($data['inactive'] ?? false)
        );
    }

    public static function fromEntity(\Ksfraser\ProjectManagement\Entity\Task $entity): self
    {
        $startDate = $entity->getStartDate() !== null ? $entity->getStartDate()->format('Y-m-d') : '';
        $endDate = $entity->getEndDate() !== null ? $entity->getEndDate()->format('Y-m-d') : null;

        return new self(
            $entity->getTaskId(),
            $entity->getProjectId(),
            $entity->getParentTaskId(),
            $entity->getName(),
            $entity->getDescription(),
            $entity->getAssignedTo(),
            null,
            $startDate,
            $endDate,
            $entity->getEstimatedHours(),
            $entity->getActualHours(),
            $entity->getProgress(),
            $entity->getPriority(),
            $entity->getStatus()
        );
    }
}