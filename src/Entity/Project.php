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

/**
 * @BABOK Related: FR-PM-001 — the Project aggregate; also implements FR-PM-004 progress and FR-PM-005 overdue
 */
class Project
{
    /** @var string */
    private $projectId;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var DateTime */
    private $startDate;
    /** @var DateTime|null */
    private $endDate;
    /** @var float */
    private $budget;
    /** @var string */
    private $customerId;
    /** @var string */
    private $projectManager;
    /** @var string */
    private $priority;
    /** @var string */
    private $status;

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
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

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getProjectId(): string
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
     * @BABOK Related: FR-PM-001 — edit a project
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @BABOK Related: FR-PM-001 — edit a project
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @BABOK Related: FR-PM-001 — set dates
     */
    public function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @BABOK Related: FR-PM-001 — set dates
     */
    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getBudget(): float
    {
        return $this->budget;
    }

    /**
     * @BABOK Related: FR-PM-001 — set budget (UC-PM-005)
     */
    public function setBudget(float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * @BABOK Related: FR-PM-001 — link to CRM customer (BR-PM-003)
     */
    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getProjectManager(): string
    {
        return $this->projectManager;
    }

    /**
     * @BABOK Related: FR-PM-001 — assign project manager (BR-PM-004)
     */
    public function setProjectManager(string $projectManager): self
    {
        $this->projectManager = $projectManager;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @BABOK Related: FR-PM-001 — set priority
     */
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-001 accessor; serves this FR data shape, adds no behaviour of its own
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @BABOK Related: FR-PM-001 — status lifecycle (BR-PM-005)
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — duration calculation
     */
    public function getDuration(): ?int
    {
        if ($this->endDate === null) {
            return null;
        }
        return $this->startDate->diff($this->endDate)->days;
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function isOverdue(): bool
    {
        if ($this->endDate === null) {
            return false;
        }
        return $this->endDate < new DateTime() && $this->status !== 'Completed';
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — active status
     */
    public function isActive(): bool
    {
        $now = new DateTime();
        return $this->startDate <= $now
            && ($this->endDate === null || $this->endDate >= $now)
            && $this->status !== 'Completed'
            && $this->status !== 'Cancelled';
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
            'start_date' => $this->startDate->format(DateTimeInterface::ATOM),
            'end_date' => $this->endDate !== null ? $this->endDate->format(DateTimeInterface::ATOM) : null,
            'budget' => $this->budget,
            'customer_id' => $this->customerId,
            'project_manager' => $this->projectManager,
            'priority' => $this->priority,
            'status' => $this->status,
        ];
    }
}