<?php
/**
 * TaskDTO - Data Transfer Object for API/UI layer
 *
 * @package Ksfraser\ProjectManagement\DTO\Task
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DTO\Task;

/**
 * @BABOK Related: FR-PM-002 — task transfer shape; also carries the FR-PM-004 progress fields and the FR-PM-005 overdue answer
 */
class TaskDTO
{
    /** @var string|null */
    private $taskId = null;
    /** @var string */
    private $projectId = '';
    /** @var string */
    private $parentTaskId = '';
    /** @var string */
    private $name = '';
    /** @var string */
    private $description = '';
    /** @var string */
    private $assignedTo = '';
    /** @var string|null */
    private $assignedToName = null;
    /** @var string */
    private $startDate = '';
    /** @var string|null */
    private $endDate = null;
    /** @var float */
    private $estimatedHours = 0.0;
    /** @var float */
    private $actualHours = 0.0;
    /** @var float */
    private $progress = 0.0;
    /** @var string */
    private $priority = 'Medium';
    /** @var string */
    private $status = 'Not Started';
    /** @var array */
    private $subtasks = [];
    /** @var array */
    private $files = [];
    /** @var array */
    private $timeEntries = [];
    /** @var string|null */
    private $createdAt = null;
    /** @var string|null */
    private $updatedAt = null;
    /** @var bool */
    private $inactive = false;

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
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

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getTaskId(): ?string
    {
        return $this->taskId;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getParentTaskId(): string
    {
        return $this->parentTaskId;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getAssignedToName(): ?string
    {
        return $this->assignedToName;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEstimatedHours(): float
    {
        return $this->estimatedHours;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getActualHours(): float
    {
        return $this->actualHours;
    }

    /**
     * @BABOK Related: FR-PM-004 — track progress 0-100%
     */
    public function getProgress(): float
    {
        return $this->progress;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getSubtasks(): array
    {
        return $this->subtasks;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @BABOK Related: FR-PM-006 — link time entries to tasks
     */
    public function getTimeEntries(): array
    {
        return $this->timeEntries;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function isInactive(): bool
    {
        return $this->inactive;
    }

    /**
     * @BABOK Related: FR-PM-004 — completion test feeding progress and overdue
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Completed' || $this->progress >= 100.0;
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function isOverdue(): bool
    {
        if (empty($this->endDate)) {
            return false;
        }
        return !$this->isCompleted() && $this->endDate < date('Y-m-d');
    }

    /**
     * @BABOK Related: FR-PM-002 — parent-child relationship (BR-TASK-001)
     */
    public function hasSubtasks(): bool
    {
        return !empty($this->subtasks);
    }

    /**
     * @BABOK Related: FR-PM-002 — estimates vs actuals (BR-TASK-004)
     */
    public function getRemainingHours(): float
    {
        return max(0.0, $this->estimatedHours - $this->actualHours);
    }

    /**
     * @BABOK Related: FR-PM-004 — task roll-up basis
     */
    public function getCompletionRatio(): float
    {
        if ($this->estimatedHours <= 0) {
            return 0.0;
        }
        return min(100.0, ($this->actualHours / $this->estimatedHours) * 100.0);
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
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

    /**
     * @BABOK Related: FR-PM-002 — hydrate the DTO from a row/API payload
     */
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

    /**
     * @BABOK Related: FR-PM-002 — project an entity into the DTO
     */
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