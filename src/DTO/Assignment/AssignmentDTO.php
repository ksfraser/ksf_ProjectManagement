<?php
/**
 * AssignmentDTO - Data Transfer Object for team assignments
 *
 * @package Ksfraser\ProjectManagement\DTO\Assignment
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DTO\Assignment;

class AssignmentDTO
{
    private string $projectId;
    private string $employeeId;
    private string $role;
    private string $startDate;
    private ?string $endDate = null;
    private float $allocationPercentage = 100.0;
    private ?string $employeeName = null;
    private ?string $email = null;
    private ?string $jobTitle = null;
    private ?string $department = null;
    private bool $isActive = true;
    private ?string $createdAt = null;

    public function __construct(
        string $projectId,
        string $employeeId,
        string $role,
        string $startDate,
        ?string $endDate = null,
        float $allocationPercentage = 100.0,
        ?string $employeeName = null,
        ?string $email = null,
        ?string $jobTitle = null,
        ?string $department = null,
        bool $isActive = true,
        ?string $createdAt = null
    ) {
        $this->projectId = $projectId;
        $this->employeeId = $employeeId;
        $this->role = $role;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->allocationPercentage = $allocationPercentage;
        $this->employeeName = $employeeName;
        $this->email = $email;
        $this->jobTitle = $jobTitle;
        $this->department = $department;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
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

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getAllocationPercentage(): float
    {
        return $this->allocationPercentage;
    }

    public function getEmployeeName(): ?string
    {
        return $this->employeeName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'project_id' => $this->projectId,
            'employee_id' => $this->employeeId,
            'role' => $this->role,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'allocation_percentage' => $this->allocationPercentage,
            'employee_name' => $this->employeeName,
            'email' => $this->email,
            'job_title' => $this->jobTitle,
            'department' => $this->department,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['project_id'] ?? '',
            $data['employee_id'] ?? '',
            $data['role'] ?? 'Team Member',
            $data['start_date'] ?? '',
            $data['end_date'] ?? null,
            (float) ($data['allocation_percentage'] ?? 100.0),
            $data['employee_name'] ?? null,
            $data['email'] ?? null,
            $data['job_title'] ?? null,
            $data['department'] ?? null,
            (bool) ($data['is_active'] ?? true),
            $data['created_at'] ?? null
        );
    }

    public static function fromEntity(\Ksfraser\ProjectManagement\Entity\ProjectAssignment $entity): self
    {
        $endDate = $entity->getEndDate() !== null ? $entity->getEndDate()->format('Y-m-d') : null;

        return new self(
            $entity->getProjectId(),
            $entity->getEmployeeId(),
            $entity->getRole(),
            $entity->getStartDate()->format('Y-m-d'),
            $endDate,
            $entity->getAllocationPercentage()
        );
    }
}