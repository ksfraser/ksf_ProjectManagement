<?php
/**
 * Project Entity
 *
 * Represents a project with tasks, team members, and progress tracking
 *
 * @package Ksfraser\ProjectManagement\Entity
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Entity;

use DateTime;
use DateTimeInterface;

class Project
{
    private string $projectId;
    private string $name;
    private string $description;
    private DateTime $startDate;
    private ?DateTime $endDate;
    private float $budget;
    private string $customerId;
    private string $projectManager;
    private string $priority;
    private string $status;

    public function __construct(
        string $projectId,
        string $name,
        string $description,
        DateTime $startDate,
        string $projectManager
    ) {
        $this->projectId = $projectId;
        $this->name = $name;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->projectManager = $projectManager;
        $this->endDate = null;
        $this->budget = 0.0;
        $this->customerId = '';
        $this->priority = 'Medium';
        $this->status = 'Planning';
    }

    public function getProjectId(): string
    {
        return $this->projectId;
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

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): self
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

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getProjectManager(): string
    {
        return $this->projectManager;
    }

    public function setProjectManager(string $projectManager): self
    {
        $this->projectManager = $projectManager;
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

    public function getDuration(): ?int
    {
        if ($this->endDate === null) {
            return null;
        }
        return $this->startDate->diff($this->endDate)->days;
    }

    public function isOverdue(): bool
    {
        if ($this->endDate === null) {
            return false;
        }
        return $this->endDate < new DateTime() && $this->status !== 'Completed';
    }

    public function isActive(): bool
    {
        $now = new DateTime();
        return $this->startDate <= $now
            && ($this->endDate === null || $this->endDate >= $now)
            && $this->status !== 'Completed'
            && $this->status !== 'Cancelled';
    }

    public function toArray(): array
    {
        return [
            'project_id' => $this->projectId,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->startDate->format(DateTimeInterface::ATOM),
            'end_date' => $this->endDate?->format(DateTimeInterface::ATOM),
            'budget' => $this->budget,
            'customer_id' => $this->customerId,
            'project_manager' => $this->projectManager,
            'priority' => $this->priority,
            'status' => $this->status,
        ];
    }
}