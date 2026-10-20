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
use ksfraser\CommonDb\Contract\DbConnectionInterface;
use Ksfraser\ProjectManagement\Entity\Project;
use Ksfraser\ProjectManagement\Repository\ProjectRepositoryInterface;

/**
 * @BABOK Related: FR-PM-001 — persistence for projects
 */
class DatabaseProjectRepository implements ProjectRepositoryInterface
{
    private const TABLE = 'ksf_projectmanagement_projects';

    /** @var DbConnectionInterface */
    private $db;

    /**
     * @BABOK Related: FR-PM-001 — DI of the transport-agnostic DbConnectionInterface
     */
    public function __construct(
        DbConnectionInterface $db
    ) {
        $this->db = $db;
    }

    /**
     * @BABOK Related: FR-PM-001 — read a project by id
     */
    public function find(string $projectId): ?Project
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE project_id = ?";
        $row = $this->db->fetchAssoc($sql, [$projectId]);

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @BABOK Related: FR-PM-001 — read every project
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-001 — persist a project
     */
    public function save(Project $project): void
    {
        $exists = $this->find($project->getProjectId()) !== null;

        if ($exists) {
            $this->update($project);
        } else {
            $this->insert($project);
        }
    }

    /**
     * @BABOK Related: FR-PM-001 — delete a project
     */
    public function delete(string $projectId): void
    {
        $sql = "DELETE FROM " . self::TABLE . " WHERE project_id = ?";
        $this->db->executeUpdate($sql, [$projectId]);
    }

    /**
     * @BABOK Related: FR-PM-001 — status lifecycle filtering (BR-PM-005)
     */
    public function findByStatus(string $status): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE status = ? ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql, [$status]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-001 — link to CRM customer (BR-PM-003)
     */
    public function findByCustomer(string $customerId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE customer_id = ? ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql, [$customerId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-001 — assign project manager (BR-PM-004)
     */
    public function findByManager(string $managerId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE project_manager = ? ORDER BY start_date DESC";
        $rows = $this->db->fetchAll($sql, [$managerId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-005 — projects past their end date
     */
    public function findOverdue(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE end_date < CURDATE()
                AND status NOT IN ('Completed', 'Cancelled')
                ORDER BY end_date ASC";

        $rows = $this->db->fetchAll($sql);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-001 — the in-scope project set for status views
     */
    public function findActive(): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE status = 'Active'
                AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY start_date DESC";

        $rows = $this->db->fetchAll($sql);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-001 — project total
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM " . self::TABLE;
        $result = $this->db->fetchAssoc($sql);

        return (int) ($result['cnt'] ?? 0);
    }

    /**
     * @BABOK Related: FR-PM-001 — status lifecycle roll-up (BR-PM-005)
     */
    public function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM " . self::TABLE . " WHERE status = ?";
        $result = $this->db->fetchAssoc($sql, [$status]);

        return (int) ($result['cnt'] ?? 0);
    }

    /**
     * @BABOK Related: FR-PM-001 — insert path of save(); no requirement of its own
     */
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
            $project->getEndDate() !== null ? $project->getEndDate()->format('Y-m-d') : null,
            $project->getBudget(),
            $project->getCustomerId(),
            $project->getProjectManager(),
            $project->getPriority(),
            $project->getStatus()
        ]);
    }

    /**
     * @BABOK Related: FR-PM-001 — update path of save(); no requirement of its own
     */
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
            $project->getEndDate() !== null ? $project->getEndDate()->format('Y-m-d') : null,
            $project->getBudget(),
            $project->getCustomerId(),
            $project->getProjectManager(),
            $project->getPriority(),
            $project->getStatus(),
            $project->getProjectId()
        ]);
    }

    /**
     * @BABOK Related: FR-PM-001 — row-to-entity mapping; no requirement of its own
     */
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