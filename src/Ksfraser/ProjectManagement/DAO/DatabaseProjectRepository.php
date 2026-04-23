<?php
/**
 * Database Project Repository
 *
 * MySQL/Doctrine DBAL implementation of ProjectRepositoryInterface
 *
 * @package Ksfraser\ProjectManagement\DAO
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DAO;

use DateTime;
use Ksfraser\ProjectManagement\Contract\DatabaseAdapterInterface;
use Ksfraser\ProjectManagement\Entity\Project;
use Ksfraser\ProjectManagement\Repository\ProjectRepositoryInterface;

class DatabaseProjectRepository implements ProjectRepositoryInterface
{
    private const TABLE = 'fa_pm_projects';

    public function __construct(
        private readonly DatabaseAdapterInterface $db
    ) {
    }

    public function find(string $projectId): ?Project
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE project_id = ?";
        $row = $this->db->fetchAssoc($sql, [$projectId]);

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function save(Project $project): void
    {
        $exists = $this->find($project->getProjectId()) !== null;

        if ($exists) {
            $this->update($project);
        } else {
            $this->insert($project);
        }
    }

    public function delete(string $projectId): void
    {
        $sql = "DELETE FROM " . self::TABLE . " WHERE project_id = ?";
        $this->db->executeUpdate($sql, [$projectId]);
    }

    public function findByStatus(string $status): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE status = ? ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql, [$status]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findByCustomer(string $customerId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE customer_id = ? ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql, [$customerId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findByManager(string $managerId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE project_manager = ? ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql, [$managerId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findOverdue(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE end_date < CURDATE()
                AND status NOT IN ('Completed', 'Cancelled')
                ORDER BY end_date ASC";

        $rows = $this->db->fetchAll($sql);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findActive(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE status = 'Active'
                AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY start_date DESC";

        $rows = $this->db->fetchAll($sql);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM " . self::TABLE;
        $result = $this->db->fetchAssoc($sql);

        return (int) ($result['cnt'] ?? 0);
    }

    public function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM " . self::TABLE . " WHERE status = ?";
        $result = $this->db->fetchAssoc($sql, [$status]);

        return (int) ($result['cnt'] ?? 0);
    }

    private function insert(Project $project): void
    {
        $sql = "INSERT INTO " . self::TABLE . " (
                    project_id, name, description, start_date, end_date,
                    budget, customer_id, project_manager, priority, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->executeUpdate($sql, [
            $project->getProjectId(),
            $project->getName(),
            $project->getDescription(),
            $project->getStartDate()->format('Y-m-d'),
            $project->getEndDate()?->format('Y-m-d'),
            $project->getBudget(),
            $project->getCustomerId(),
            $project->getProjectManager(),
            $project->getPriority(),
            $project->getStatus()
        ]);
    }

    private function update(Project $project): void
    {
        $sql = "UPDATE " . self::TABLE . " SET
                    name = ?,
                    description = ?,
                    start_date = ?,
                    end_date = ?,
                    budget = ?,
                    customer_id = ?,
                    project_manager = ?,
                    priority = ?,
                    status = ?
                WHERE project_id = ?";

        $this->db->executeUpdate($sql, [
            $project->getName(),
            $project->getDescription(),
            $project->getStartDate()->format('Y-m-d'),
            $project->getEndDate()?->format('Y-m-d'),
            $project->getBudget(),
            $project->getCustomerId(),
            $project->getProjectManager(),
            $project->getPriority(),
            $project->getStatus(),
            $project->getProjectId()
        ]);
    }

    private function hydrate(array $row): Project
    {
        $project = new Project(
            $row['project_id'],
            $row['name'],
            $row['description'] ?? '',
            new DateTime($row['start_date']),
            $row['project_manager']
        );

        if (!empty($row['end_date'])) {
            $project->setEndDate(new DateTime($row['end_date']));
        }

        $project->setBudget((float) ($row['budget'] ?? 0));
        $project->setCustomerId($row['customer_id'] ?? '');
        $project->setPriority($row['priority'] ?? 'Medium');
        $project->setStatus($row['status'] ?? 'Planning');

        return $project;
    }
}