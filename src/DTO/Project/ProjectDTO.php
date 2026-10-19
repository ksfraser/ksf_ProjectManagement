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

    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getProjectManager(): string
    {
        return $this->projectManager;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTaskCount(): int
    {
        return $this->taskCount;
    }

    public function getCompletedTaskCount(): int
    {
        return $this->completedTaskCount;
    }

    public function getOverallProgress(): float
    {
        return $this->overallProgress;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getTeam(): array
    {
        return $this->team;
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

    public function getDurationDays(): ?int
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            return null;
        }

        $start = new DateTime($this->startDate);
        $end = new DateTime($this->endDate);

        return $start->diff($end)->days;
    }

    public function isOverdue(): bool
    {
        if (empty($this->endDate)) {
            return false;
        }

        $end = new DateTime($this->endDate);
        return $end < new DateTime() && $this->status !== 'Completed';
    }

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