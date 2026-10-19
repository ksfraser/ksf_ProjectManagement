<?php
/**
 * ProjectAssignment Repository Interface
 *
 * @package Ksfraser\ProjectManagement\Repository
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Repository;

use Ksfraser\ProjectManagement\Entity\ProjectAssignment;

/**
 * @BABOK Related: FR-PM-003 — the persistence contract for employee allocations
 */
interface AssignmentRepositoryInterface
{
    /**
     * @BABOK Related: FR-PM-003 — allocation persistence contract
     */
    public function findByProjectAndEmployee(string $projectId, string $employeeId): ?ProjectAssignment;

    /**
     * @BABOK Related: FR-PM-003 — allocation persistence contract
     */
    public function findByProject(string $projectId): array;

    /**
     * @BABOK Related: FR-PM-003 — allocation persistence contract
     */
    public function findByEmployee(string $employeeId): array;

    /**
     * @BABOK Related: FR-PM-003 — allocation persistence contract
     */
    public function findActiveByProject(string $projectId): array;

    /**
     * @BABOK Related: FR-PM-003 — allocation persistence contract
     */
    public function save(ProjectAssignment $assignment): void;

    /**
     * @BABOK Related: FR-PM-003 — allocation persistence contract
     */
    public function delete(string $projectId, string $employeeId): void;
}