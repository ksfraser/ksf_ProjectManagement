<?php
/**
 * Task Repository Interface
 *
 * @package Ksfraser\ProjectManagement\Repository
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Repository;

use Ksfraser\ProjectManagement\Entity\Task;

/**
 * @BABOK Related: FR-PM-002 — the persistence contract for tasks
 */
interface TaskRepositoryInterface
{
    /**
     * @BABOK Related: FR-PM-002 — task persistence contract
     */
    public function find(string $taskId): ?Task;

    /**
     * @BABOK Related: FR-PM-002 — task persistence contract
     */
    public function findByProject(string $projectId): array;

    /**
     * @BABOK Related: FR-PM-002 — parent-child hierarchy (BR-TASK-001)
     */
    public function findByParent(string $parentTaskId): array;

    /**
     * @BABOK Related: FR-PM-002 — task persistence contract
     */
    public function save(Task $task): void;

    /**
     * @BABOK Related: FR-PM-002 — task persistence contract
     */
    public function delete(string $taskId): void;

    /**
     * @BABOK Related: FR-PM-002 — task assignment (BR-TASK-002)
     */
    public function findByAssignee(string $employeeId): array;

    /**
     * @BABOK Related: FR-PM-005 — overdue tasks
     */
    public function findOverdue(): array;
}