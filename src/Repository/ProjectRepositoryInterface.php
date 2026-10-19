<?php
/**
 * Project Repository Interface
 *
 * @package Ksfraser\ProjectManagement\Repository
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Repository;

use Ksfraser\ProjectManagement\Entity\Project;

/**
 * @BABOK Related: FR-PM-001 — the persistence contract for projects
 */
interface ProjectRepositoryInterface
{
    /**
     * @BABOK Related: FR-PM-001 — project persistence contract
     */
    public function find(string $projectId): ?Project;

    /**
     * @BABOK Related: FR-PM-001 — project persistence contract
     */
    public function findAll(): array;

    /**
     * @BABOK Related: FR-PM-001 — project persistence contract
     */
    public function save(Project $project): void;

    /**
     * @BABOK Related: FR-PM-001 — project persistence contract
     */
    public function delete(string $projectId): void;

    /**
     * @BABOK Related: FR-PM-001 — status lifecycle (BR-PM-005)
     */
    public function findByStatus(string $status): array;

    /**
     * @BABOK Related: FR-PM-001 — link to CRM customer (BR-PM-003)
     */
    public function findByCustomer(string $customerId): array;

    /**
     * @BABOK Related: FR-PM-001 — assign project manager (BR-PM-004)
     */
    public function findByManager(string $managerId): array;

    /**
     * @BABOK Related: FR-PM-005 — overdue projects
     */
    public function findOverdue(): array;
}