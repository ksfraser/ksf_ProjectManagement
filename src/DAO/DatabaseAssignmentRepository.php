<?php
/**
 * Database Assignment Repository
 *
 * @package Ksfraser\ProjectManagement\DAO
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DAO;

use DateTime;
use ksfraser\CommonDb\Contract\DbConnectionInterface;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use Ksfraser\ProjectManagement\Repository\AssignmentRepositoryInterface;

/**
 * @BABOK Related: FR-PM-003 — persistence for employee-to-project allocations
 */
class DatabaseAssignmentRepository implements AssignmentRepositoryInterface
{
    private const TABLE = 'ksf_projectmanagement_assignments';

    /** @var DbConnectionInterface */
    private $db;

    /**
     * @BABOK Related: FR-PM-003 — DI of the transport-agnostic DbConnectionInterface
     */
    public function __construct(
        DbConnectionInterface $db
    ) {
        $this->db = $db;
    }

    /**
     * @BABOK Related: FR-PM-003 — look up one allocation
     */
    public function findByProjectAndEmployee(string $projectId, string $employeeId): ?ProjectAssignment
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE project_id = ? AND employee_id = ?";
        $row = $this->db->fetchAssoc($sql, [$projectId, $employeeId]);

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @BABOK Related: FR-PM-003 — allocations of a project
     */
    public function findByProject(string $projectId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE project_id = ? ORDER BY role, employee_id";
        $rows = $this->db->fetchAll($sql, [$projectId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-003 — allocations of an employee
     */
    public function findByEmployee(string $employeeId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE employee_id = ? ORDER BY project_id";
        $rows = $this->db->fetchAll($sql, [$employeeId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-003 — currently-active allocations of a project
     */
    public function findActiveByProject(string $projectId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE project_id = ?
                AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY role, employee_id";
        $rows = $this->db->fetchAll($sql, [$projectId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-003 — currently-active allocations of an employee
     */
    public function findActiveByEmployee(string $employeeId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE employee_id = ?
                AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY project_id";
        $rows = $this->db->fetchAll($sql, [$employeeId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-003 — persist an allocation
     */
    public function save(ProjectAssignment $assignment): void
    {
        $exists = $this->findByProjectAndEmployee(
            $assignment->getProjectId(),
            $assignment->getEmployeeId()
        ) !== null;

        if ($exists) {
            $this->update($assignment);
        } else {
            $this->insert($assignment);
        }
    }

    /**
     * @BABOK Related: FR-PM-003 — remove an allocation
     */
    public function delete(string $projectId, string $employeeId): void
    {
        $sql = "DELETE FROM " . self::TABLE . "
                WHERE project_id = ? AND employee_id = ?";
        $this->db->executeUpdate($sql, [$projectId, $employeeId]);
    }

    /**
     * @BABOK Related: FR-PM-003 — insert path of save(); no requirement of its own
     */
    private function insert(ProjectAssignment $assignment): void
    {
        $sql = "INSERT INTO " . self::TABLE . " (
                    project_id, employee_id, role, start_date, end_date, allocation_percentage
                ) VALUES (?, ?, ?, ?, ?, ?)";

        $this->db->executeUpdate($sql, [
            $assignment->getProjectId(),
            $assignment->getEmployeeId(),
            $assignment->getRole(),
            $assignment->getStartDate()->format('Y-m-d'),
            $assignment->getEndDate() !== null ? $assignment->getEndDate()->format('Y-m-d') : null,
            $assignment->getAllocationPercentage()
        ]);
    }

    /**
     * @BABOK Related: FR-PM-003 — update path of save(); no requirement of its own
     */
    private function update(ProjectAssignment $assignment): void
    {
        $sql = "UPDATE " . self::TABLE . " SET
                    role = ?,
                    start_date = ?,
                    end_date = ?,
                    allocation_percentage = ?
                WHERE project_id = ? AND employee_id = ?";

        $this->db->executeUpdate($sql, [
            $assignment->getRole(),
            $assignment->getStartDate()->format('Y-m-d'),
            $assignment->getEndDate() !== null ? $assignment->getEndDate()->format('Y-m-d') : null,
            $assignment->getAllocationPercentage(),
            $assignment->getProjectId(),
            $assignment->getEmployeeId()
        ]);
    }

    /**
     * @BABOK Related: FR-PM-003 — row-to-entity mapping; no requirement of its own
     */
    private function hydrate(array $row): ProjectAssignment
    {
        $assignment = new ProjectAssignment(
            $row['project_id'],
            $row['employee_id'],
            $row['role'] ?? 'Team Member',
            new DateTime($row['start_date']),
            (float) ($row['allocation_percentage'] ?? 100.0)
        );

        if (!empty($row['end_date'])) {
            $assignment->setEndDate(new DateTime($row['end_date']));
        }

        return $assignment;
    }
}