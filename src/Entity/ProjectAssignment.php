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

/**
 * @BABOK Related: FR-PM-003 — the employee-to-project allocation value object
 */
class ProjectAssignment
{
    /** @var string */
    private $projectId;
    /** @var string */
    private $employeeId;
    /** @var string */
    private $role;
    /** @var DateTime */
    private $startDate;
    /** @var DateTime|null */
    private $endDate;
    /** @var float */
    private $allocationPercentage;

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
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

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @BABOK Related: FR-PM-003 — edit an allocation
     */
    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @BABOK Related: FR-PM-003 — edit an allocation window
     */
    public function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @BABOK Related: FR-PM-003 — end an allocation
     */
    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getAllocationPercentage(): float
    {
        return $this->allocationPercentage;
    }

    /**
     * @BABOK Related: FR-PM-003 — allocation % drives workload and over-allocation
     */
    public function setAllocationPercentage(float $allocationPercentage): self
    {
        $this->allocationPercentage = max(0.0, min(100.0, $allocationPercentage));
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — within date range and not ended
     */
    public function isActive(): bool
    {
        $now = new DateTime();
        return $this->startDate <= $now
            && ($this->endDate === null || $this->endDate >= $now);
    }

    /**
     * @BABOK Related: FR-PM-003 — track active/ended allocations
     */
    public function isEnded(): bool
    {
        if ($this->endDate === null) {
            return false;
        }
        return $this->endDate < new DateTime();
    }

    /**
     * @BABOK Related: FR-PM-003 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function toArray(): array
    {
        return [
            'project_id' => $this->projectId,
            'employee_id' => $this->employeeId,
            'role' => $this->role,
            'start_date' => $this->startDate->format(DateTimeInterface::ATOM),
            'end_date' => $this->endDate !== null ? $this->endDate->format(DateTimeInterface::ATOM) : null,
            'allocation_percentage' => $this->allocationPercentage,
        ];
    }
}