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
    public function __construct(
        private readonly ?string $projectId = null,
        private readonly string $name = '',
        private readonly string $description = '',
        private readonly string $startDate = '',
        private readonly ?string $endDate = null,
        private readonly float $budget = 0.0,
        private readonly string $customerId = '',
        private readonly string $projectManager = '',
        private readonly string $priority = 'Medium',
        private readonly string $status = 'Planning',
        private readonly int $taskCount = 0,
        private readonly int $completedTaskCount = 0,
        private readonly float $overallProgress = 0.0,
        private readonly array $files = [],
        private readonly array $team = [],
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null,
        private readonly bool $inactive = false
    ) {
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
            projectId: $data['project_id'] ?? null,
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            startDate: $data['start_date'] ?? '',
            endDate: $data['end_date'] ?? null,
            budget: (float) ($data['budget'] ?? 0.0),
            customerId: $data['customer_id'] ?? '',
            projectManager: $data['project_manager'] ?? '',
            priority: $data['priority'] ?? 'Medium',
            status: $data['status'] ?? 'Planning',
            taskCount: (int) ($data['task_count'] ?? 0),
            completedTaskCount: (int) ($data['completed_task_count'] ?? 0),
            overallProgress: (float) ($data['overall_progress'] ?? 0.0),
            files: $data['files'] ?? [],
            team: $data['team'] ?? [],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            inactive: (bool) ($data['inactive'] ?? false)
        );
    }

    public static function fromEntity(\Ksfraser\ProjectManagement\Entity\Project $entity): self
    {
        return new self(
            projectId: $entity->getProjectId(),
            name: $entity->getName(),
            description: $entity->getDescription(),
            startDate: $entity->getStartDate()->format('Y-m-d'),
            endDate: $entity->getEndDate()?->format('Y-m-d'),
            budget: $entity->getBudget(),
            customerId: $entity->getCustomerId(),
            projectManager: $entity->getProjectManager(),
            priority: $entity->getPriority(),
            status: $entity->getStatus()
        );
    }
}