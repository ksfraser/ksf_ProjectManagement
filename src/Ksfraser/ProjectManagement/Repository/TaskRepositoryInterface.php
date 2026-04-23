<?php
/**
 * Task Repository Interface
 *
 * @package Ksfraser\ProjectManagement\Repository
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Repository;

use Ksfraser\ProjectManagement\Entity\Task;

interface TaskRepositoryInterface
{
    public function find(string $taskId): ?Task;

    public function findByProject(string $projectId): array;

    public function findByParent(string $parentTaskId): array;

    public function save(Task $task): void;

    public function delete(string $taskId): void;

    public function findByAssignee(string $employeeId): array;

    public function findOverdue(): array;
}