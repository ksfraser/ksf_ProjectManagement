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

/**
 * @BABOK Related: FR-PM-002 — the Task aggregate; also implements FR-PM-004 progress and FR-PM-005 overdue
 */
class Task
{
    /** @var string */
    private $taskId;
    /** @var string */
    private $projectId;
    /** @var string */
    private $parentTaskId;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var string */
    private $assignedTo;
    /** @var DateTime|null */
    private $startDate;
    /** @var DateTime|null */
    private $endDate;
    /** @var float */
    private $estimatedHours;
    /** @var float */
    private $actualHours;
    /** @var float */
    private $progress;
    /** @var string */
    private $priority;
    /** @var string */
    private $status;

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
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

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getTaskId(): string
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
     * @BABOK Related: FR-PM-002 — parent-child hierarchy (BR-TASK-001)
     */
    public function setParentTaskId(string $parentTaskId): self
    {
        $this->parentTaskId = $parentTaskId;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @BABOK Related: FR-PM-002 — edit a task
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @BABOK Related: FR-PM-002 — edit a task
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    /**
     * @BABOK Related: FR-PM-002 — task assignment (BR-TASK-002)
     */
    public function setAssignedTo(string $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @BABOK Related: FR-PM-002 — set dates
     */
    public function setStartDate(?DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @BABOK Related: FR-PM-002 — set dates
     */
    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEstimatedHours(): float
    {
        return $this->estimatedHours;
    }

    /**
     * @BABOK Related: FR-PM-002 — estimates vs actuals (BR-TASK-004)
     */
    public function setEstimatedHours(float $estimatedHours): self
    {
        $this->estimatedHours = $estimatedHours;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getActualHours(): float
    {
        return $this->actualHours;
    }

    /**
     * @BABOK Related: FR-PM-002 — estimates vs actuals (BR-TASK-004)
     */
    public function setActualHours(float $actualHours): self
    {
        $this->actualHours = $actualHours;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getProgress(): float
    {
        return $this->progress;
    }

    /**
     * @BABOK Related: FR-PM-004 — track progress 0-100%
     */
    public function setProgress(float $progress): self
    {
        $this->progress = max(0.0, min(100.0, $progress));
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @BABOK Related: FR-PM-002 — priority assignment (BR-TASK-006)
     */
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-002 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @BABOK Related: FR-PM-002 — status lifecycle
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-004 — completion test
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
        if ($this->endDate === null) {
            return false;
        }
        return $this->endDate < new DateTime() && !$this->isCompleted();
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — duration calculation
     */
    public function getDuration(): ?int
    {
        if ($this->startDate === null || $this->endDate === null) {
            return null;
        }
        return $this->startDate->diff($this->endDate)->days;
    }

    /**
     * @BABOK Related: FR-PM-002 — parent-child hierarchy (BR-TASK-001)
     */
    public function hasSubtasks(): bool
    {
        return false;
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
            'start_date' => $this->startDate !== null ? $this->startDate->format(DateTimeInterface::ATOM) : null,
            'end_date' => $this->endDate !== null ? $this->endDate->format(DateTimeInterface::ATOM) : null,
            'estimated_hours' => $this->estimatedHours,
            'actual_hours' => $this->actualHours,
            'progress' => $this->progress,
            'priority' => $this->priority,
            'status' => $this->status,
        ];
    }
}