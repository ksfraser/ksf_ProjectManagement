<?php
/**
 * ProjectDTO - Data Transfer Object for API/UI layer
 *
 * Separate from domain Entity to decouple persistence from domain logic.
 * Used for: JSON serialization, API responses, form data transfer.
 *
 * @package Ksfraser\ProjectManagement\DTO\Project
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DTO\Project;

use DateTime;
use DateTimeInterface;

/**
 * @BABOK Related: FR-PM-001 — project transfer shape; also carries the FR-PM-004 progress fields and the FR-PM-005 overdue answer
 */
class ProjectDTO
{
    /** @var string|null */
    private $projectId = null;
    /** @var string */
    private $name = '';
    /** @var string */
    private $description = '';
    /** @var string */
    private $startDate = '';
    /** @var string|null */
    private $endDate = null;
    /** @var float */
    private $budget = 0.0;
    /** @var string */
    private $customerId = '';
    /** @var string */
    private $projectManager = '';
    /** @var string */
    private $priority = 'Medium';
    /** @var string */
    private $status = 'Planning';
    /** @var int */
    private $taskCount = 0;
    /** @var int */
    private $completedTaskCount = 0;
    /** @var float */
    private $overallProgress = 0.0;
    /** @var array */
    private $files = [];
    /** @var array */
    private $team = [];
    /** @var string|null */
    private $createdAt = null;
    /** @var string|null */
    private $updatedAt = null;
    /** @var bool */
    private $inactive = false;

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function __construct(
        ?string $projectId = null,
        string $name = '',
        string $description = '',
        string $startDate = '',
        ?string $endDate = null,
        float $budget = 0.0,
        string $customerId = '',
        string $projectManager = '',
        string $priority = 'Medium',
        string $status = 'Planning',
        int $taskCount = 0,
        int $completedTaskCount = 0,
        float $overallProgress = 0.0,
        array $files = [],
        array $team = [],
        ?string $createdAt = null,
        ?string $updatedAt = null,
        bool $inactive = false
    ) {
        $this->projectId = $projectId;
        $this->name = $name;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->budget = $budget;
        $this->customerId = $customerId;
        $this->projectManager = $projectManager;
        $this->priority = $priority;
        $this->status = $status;
        $this->taskCount = $taskCount;
        $this->completedTaskCount = $completedTaskCount;
        $this->overallProgress = $overallProgress;
        $this->files = $files;
        $this->team = $team;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->inactive = $inactive;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getBudget(): float
    {
        return $this->budget;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getProjectManager(): string
    {
        return $this->projectManager;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @BABOK Related: FR-PM-004 — progress denominator
     */
    public function getTaskCount(): int
    {
        return $this->taskCount;
    }

    /**
     * @BABOK Related: FR-PM-004 — progress numerator
     */
    public function getCompletedTaskCount(): int
    {
        return $this->completedTaskCount;
    }

    /**
     * @BABOK Related: FR-PM-004 — aggregate task progress to project
     */
    public function getOverallProgress(): float
    {
        return $this->overallProgress;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getTeam(): array
    {
        return $this->team;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function isInactive(): bool
    {
        return $this->inactive;
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — days between start and end
     */
    public function getDurationDays(): ?int
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            return null;
        }

        $start = new DateTime($this->startDate);
        $end = new DateTime($this->endDate);

        return $start->diff($end)->days;
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function isOverdue(): bool
    {
        if (empty($this->endDate)) {
            return false;
        }

        $end = new DateTime($this->endDate);
        return $end < new DateTime() && $this->status !== 'Completed';
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function toArray(): array
    {
        return [
            'project_id' => $this->projectId,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'budget' => $this->budget,
            'customer_id' => $this->customerId,
            'project_manager' => $this->projectManager,
            'priority' => $this->priority,
            'status' => $this->status,
            'task_count' => $this->taskCount,
            'completed_task_count' => $this->completedTaskCount,
            'overall_progress' => $this->overallProgress,
            'files' => $this->files,
            'team' => $this->team,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'inactive' => $this->inactive,
        ];
    }

    /**
     * @BABOK Related: FR-PM-001 — hydrate the DTO from a row/API payload
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['project_id'] ?? null,
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['start_date'] ?? '',
            $data['end_date'] ?? null,
            (float) ($data['budget'] ?? 0.0),
            $data['customer_id'] ?? '',
            $data['project_manager'] ?? '',
            $data['priority'] ?? 'Medium',
            $data['status'] ?? 'Planning',
            (int) ($data['task_count'] ?? 0),
            (int) ($data['completed_task_count'] ?? 0),
            (float) ($data['overall_progress'] ?? 0.0),
            $data['files'] ?? [],
            $data['team'] ?? [],
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
            (bool) ($data['inactive'] ?? false)
        );
    }

    /**
     * @BABOK Related: FR-PM-001 — project an entity into the DTO
     */
    public static function fromEntity(\Ksfraser\ProjectManagement\Entity\Project $entity): self
    {
        $endDate = $entity->getEndDate() !== null ? $entity->getEndDate()->format('Y-m-d') : null;

        return new self(
            $entity->getProjectId(),
            $entity->getName(),
            $entity->getDescription(),
            $entity->getStartDate()->format('Y-m-d'),
            $endDate,
            $entity->getBudget(),
            $entity->getCustomerId(),
            $entity->getProjectManager(),
            $entity->getPriority(),
            $entity->getStatus()
        );
    }
}