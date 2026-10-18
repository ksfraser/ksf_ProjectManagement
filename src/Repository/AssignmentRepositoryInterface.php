<?php
/**
 * ProjectAssignment Repository Interface
 *
 * @package Ksfraser\ProjectManagement\Repository
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Repository;

use Ksfraser\ProjectManagement\Entity\ProjectAssignment;

interface AssignmentRepositoryInterface
{
    public function findByProjectAndEmployee(string $projectId, string $employeeId): ?ProjectAssignment;

    public function findByProject(string $projectId): array;

    public function findByEmployee(string $employeeId): array;

    public function findActiveByProject(string $projectId): array;

    public function save(ProjectAssignment $assignment): void;

    public function delete(string $projectId, string $employeeId): void;
}