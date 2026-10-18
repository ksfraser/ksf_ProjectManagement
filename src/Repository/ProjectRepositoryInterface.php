<?php
/**
 * Project Repository Interface
 *
 * @package Ksfraser\ProjectManagement\Repository
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Repository;

use Ksfraser\ProjectManagement\Entity\Project;

interface ProjectRepositoryInterface
{
    public function find(string $projectId): ?Project;

    public function findAll(): array;

    public function save(Project $project): void;

    public function delete(string $projectId): void;

    public function findByStatus(string $status): array;

    public function findByCustomer(string $customerId): array;

    public function findByManager(string $managerId): array;

    public function findOverdue(): array;
}