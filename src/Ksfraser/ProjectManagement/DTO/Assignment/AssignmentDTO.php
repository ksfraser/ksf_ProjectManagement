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
    public function __construct(
        private readonly string $projectId,
        private readonly string $employeeId,
        private readonly string $role,
        private readonly string $startDate,
        private readonly ?string $endDate = null,
        private readonly float $allocationPercentage = 100.0,
        private readonly ?string $employeeName = null,
        private readonly ?string $email = null,
        private readonly ?string $jobTitle = null,
        private readonly ?string $department = null,
        private readonly bool $isActive = true,
        private readonly ?string $createdAt = null
    ) {
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
            projectId: $data['project_id'] ?? '',
            employeeId: $data['employee_id'] ?? '',
            role: $data['role'] ?? 'Team Member',
            startDate: $data['start_date'] ?? '',
            endDate: $data['end_date'] ?? null,
            allocationPercentage: (float) ($data['allocation_percentage'] ?? 100.0),
            employeeName: $data['employee_name'] ?? null,
            email: $data['email'] ?? null,
            jobTitle: $data['job_title'] ?? null,
            department: $data['department'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            createdAt: $data['created_at'] ?? null
        );
    }

    public static function fromEntity(\Ksfraser\ProjectManagement\Entity\ProjectAssignment $entity): self
    {
        return new self(
            projectId: $entity->getProjectId(),
            employeeId: $entity->getEmployeeId(),
            role: $entity->getRole(),
            startDate: $entity->getStartDate()->format('Y-m-d'),
            endDate: $entity->getEndDate()?->format('Y-m-d'),
            allocationPercentage: $entity->getAllocationPercentage()
        );
    }
}