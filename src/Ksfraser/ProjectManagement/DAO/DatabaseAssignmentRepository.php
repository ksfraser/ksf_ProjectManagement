<?php
/**
 * Database Assignment Repository
 *
 * @package Ksfraser\ProjectManagement\DAO
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DAO;

use DateTime;
use Ksfraser\ProjectManagement\Contract\DatabaseAdapterInterface;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use Ksfraser\ProjectManagement\Repository\AssignmentRepositoryInterface;

class DatabaseAssignmentRepository implements AssignmentRepositoryInterface
{
    private const TABLE = 'fa_pm_assignments';

    public function __construct(
        private readonly DatabaseAdapterInterface $db
    ) {
    }

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

    public function findByProject(string $projectId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE project_id = ? ORDER BY role, employee_id";
        $rows = $this->db->fetchAll($sql, [$projectId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findByEmployee(string $employeeId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE employee_id = ? ORDER BY project_id";
        $rows = $this->db->fetchAll($sql, [$employeeId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findActiveByProject(string $projectId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE project_id = ?
                AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY role, employee_id";
        $rows = $this->db->fetchAll($sql, [$projectId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findActiveByEmployee(string $employeeId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . "
                WHERE employee_id = ?
                AND (end_date IS NULL OR end_date >= CURDATE())
                ORDER BY project_id";
        $rows = $this->db->fetchAll($sql, [$employeeId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

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

    public function delete(string $projectId, string $employeeId): void
    {
        $sql = "DELETE FROM " . self::TABLE . "
                WHERE project_id = ? AND employee_id = ?";
        $this->db->executeUpdate($sql, [$projectId, $employeeId]);
    }

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
            $assignment->getEndDate()?->format('Y-m-d'),
            $assignment->getAllocationPercentage()
        ]);
    }

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
            $assignment->getEndDate()?->format('Y-m-d'),
            $assignment->getAllocationPercentage(),
            $assignment->getProjectId(),
            $assignment->getEmployeeId()
        ]);
    }

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