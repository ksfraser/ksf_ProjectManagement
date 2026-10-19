<?php
/**
 * Database Task Repository
 *
 * @package Ksfraser\ProjectManagement\DAO
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DAO;

use DateTime;
use ksfraser\CommonDb\Contract\DbConnectionInterface;
use Ksfraser\ProjectManagement\Entity\Task;
use Ksfraser\ProjectManagement\Repository\TaskRepositoryInterface;

/**
 * @BABOK Related: FR-PM-002 — persistence for tasks
 */
class DatabaseTaskRepository implements TaskRepositoryInterface
{
    private const TABLE = 'fa_pm_tasks';

    /** @var DbConnectionInterface */
    private $db;

    /**
     * @BABOK Related: FR-PM-002 — DI of the transport-agnostic DbConnectionInterface
     */
    public function __construct(
        DbConnectionInterface $db
    ) {
        $this->db = $db;
    }

    /**
     * @BABOK Related: FR-PM-002 — read a task by id
     */
    public function find(string $taskId): ?Task
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE task_id = ?";
        $row = $this->db->fetchAssoc($sql, [$taskId]);

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @BABOK Related: FR-PM-002 — the tasks of a project
     */
    public function findByProject(string $projectId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE project_id = ? ORDER BY parent_task_id, task_id";
        $rows = $this->db->fetchAll($sql, [$projectId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-002 — hierarchical parent-child relationships (BR-TASK-001)
     */
    public function findByParent(string $parentTaskId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE parent_task_id = ? ORDER BY task_id";
        $rows = $this->db->fetchAll($sql, [$parentTaskId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-002 — persist a task
     */
    public function save(Task $task): void
    {
        $exists = $this->find($task->getTaskId()) !== null;

        if ($exists) {
            $this->update($task);
        } else {
            $this->insert($task);
        }
    }

    /**
     * @BABOK Related: FR-PM-002 — delete a task
     */
    public function delete(string $taskId): void
    {
        $sql = "DELETE FROM " . self::TABLE . " WHERE task_id = ?";
        $this->db->executeUpdate($sql, [$taskId]);
    }

    /**
     * @BABOK Related: FR-PM-002 — task assignment lookup (BR-TASK-002)
     */
    public function findByAssignee(string $employeeId): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE assigned_to = ? ORDER BY task_id";
        $rows = $this->db->fetchAll($sql, [$employeeId]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-005 — tasks past their due date
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
     * @BABOK Related: FR-PM-002 — status filtering
     */
    public function findByStatus(string $status): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE status = ? ORDER BY task_id";
        $rows = $this->db->fetchAll($sql, [$status]);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    /**
     * @BABOK Related: FR-PM-004 — denominator of project progress
     */
    public function countByProject(string $projectId): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM " . self::TABLE . " WHERE project_id = ?";
        $result = $this->db->fetchAssoc($sql, [$projectId]);

        return (int) ($result['cnt'] ?? 0);
    }

    /**
     * @BABOK Related: FR-PM-004 — numerator of project progress
     */
    public function countCompletedByProject(string $projectId): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM " . self::TABLE . "
                WHERE project_id = ? AND status = 'Completed'";
        $result = $this->db->fetchAssoc($sql, [$projectId]);

        return (int) ($result['cnt'] ?? 0);
    }

    /**
     * @BABOK Related: FR-PM-002 — insert path of save(); no requirement of its own
     */
    private function insert(Task $task): void
    {
        $sql = "INSERT INTO " . self::TABLE . " (
                    task_id, project_id, parent_task_id, name, description,
                    assigned_to, start_date, end_date, estimated_hours,
                    actual_hours, progress, priority, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->executeUpdate($sql, [
            $task->getTaskId(),
            $task->getProjectId(),
            $task->getParentTaskId(),
            $task->getName(),
            $task->getDescription(),
            $task->getAssignedTo(),
            $task->getStartDate() !== null ? $task->getStartDate()->format('Y-m-d') : null,
            $task->getEndDate() !== null ? $task->getEndDate()->format('Y-m-d') : null,
            $task->getEstimatedHours(),
            $task->getActualHours(),
            $task->getProgress(),
            $task->getPriority(),
            $task->getStatus()
        ]);
    }

    /**
     * @BABOK Related: FR-PM-002 — update path of save(); no requirement of its own
     */
    private function update(Task $task): void
    {
        $sql = "UPDATE " . self::TABLE . " SET
                    parent_task_id = ?,
                    name = ?,
                    description = ?,
                    assigned_to = ?,
                    start_date = ?,
                    end_date = ?,
                    estimated_hours = ?,
                    actual_hours = ?,
                    progress = ?,
                    priority = ?,
                    status = ?
                WHERE task_id = ?";

        $this->db->executeUpdate($sql, [
            $task->getParentTaskId(),
            $task->getName(),
            $task->getDescription(),
            $task->getAssignedTo(),
            $task->getStartDate() !== null ? $task->getStartDate()->format('Y-m-d') : null,
            $task->getEndDate() !== null ? $task->getEndDate()->format('Y-m-d') : null,
            $task->getEstimatedHours(),
            $task->getActualHours(),
            $task->getProgress(),
            $task->getPriority(),
            $task->getStatus(),
            $task->getTaskId()
        ]);
    }

    /**
     * @BABOK Related: FR-PM-002 — row-to-entity mapping; no requirement of its own
     */
    private function hydrate(array $row): Task
    {
        $task = new Task(
            $row['task_id'],
            $row['project_id'],
            $row['name'],
            $row['description'] ?? '',
            $row['assigned_to'] ?? ''
        );

        $task->setParentTaskId($row['parent_task_id'] ?? '');

        if (!empty($row['start_date'])) {
            $task->setStartDate(new DateTime($row['start_date']));
        }
        if (!empty($row['end_date'])) {
            $task->setEndDate(new DateTime($row['end_date']));
        }

        $task->setEstimatedHours((float) ($row['estimated_hours'] ?? 0));
        $task->setActualHours((float) ($row['actual_hours'] ?? 0));
        $task->setProgress((float) ($row['progress'] ?? 0));
        $task->setPriority($row['priority'] ?? 'Medium');
        $task->setStatus($row['status'] ?? 'Not Started');

        return $task;
    }
}