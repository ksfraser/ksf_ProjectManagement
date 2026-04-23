<?php
/**
 * ProjectAssignment Entity
 *
 * Represents employee assignment to a project
 *
 * @package Ksfraser\ProjectManagement\Entity
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Entity;

use DateTime;
use DateTimeInterface;

class ProjectAssignment
{
    private string $projectId;
    private string $employeeId;
    private string $role;
    private DateTime $startDate;
    private ?DateTime $endDate;
    private float $allocationPercentage;

    public function __construct(
        string $projectId,
        string $employeeId,
        string $role,
        DateTime $startDate,
        float $allocationPercentage = 100.0
    ) {
        $this->projectId = $projectId;
        $this->employeeId = $employeeId;
        $this->role = $role;
        $this->startDate = $startDate;
        $this->allocationPercentage = $allocationPercentage;
        $this->endDate = null;
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

    public function setRole(string $role): self
    {
        $this->role = $role;
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

    public function getAllocationPercentage(): float
    {
        return $this->allocationPercentage;
    }

    public function setAllocationPercentage(float $allocationPercentage): self
    {
        $this->allocationPercentage = max(0.0, min(100.0, $allocationPercentage));
        return $this;
    }

    public function isActive(): bool
    {
        $now = new DateTime();
        return $this->startDate <= $now
            && ($this->endDate === null || $this->endDate >= $now);
    }

    public function isEnded(): bool
    {
        if ($this->endDate === null) {
            return false;
        }
        return $this->endDate < new DateTime();
    }

    public function toArray(): array
    {
        return [
            'project_id' => $this->projectId,
            'employee_id' => $this->employeeId,
            'role' => $this->role,
            'start_date' => $this->startDate->format(DateTimeInterface::ATOM),
            'end_date' => $this->endDate?->format(DateTimeInterface::ATOM),
            'allocation_percentage' => $this->allocationPercentage,
        ];
    }
}