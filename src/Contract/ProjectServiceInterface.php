<?php
/**
 * Project Service Interface
 *
 * @package Ksfraser\ProjectManagement\Contract
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Contract;

use Ksfraser\ProjectManagement\Entity\Project;
use Ksfraser\ProjectManagement\Entity\Task;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use Ksfraser\ProjectManagement\Exception\ProjectException;

/**
 * @BABOK Related: FR-PM-001, FR-PM-002, FR-PM-003, FR-PM-004 — the aggregate service contract spanning project CRUD, tasks, allocation and progress
 */
interface ProjectServiceInterface
{
    /**
     * @BABOK Related: FR-PM-001 — create a project
     */
    public function createProject(array $projectData): Project;

    /**
     * @BABOK Related: FR-PM-001 — read a project by id
     */
    public function getProject(string $projectId): Project;

    /**
     * @BABOK Related: FR-PM-001 — edit a project
     */
    public function updateProject(string $projectId, array $projectData): Project;

    /**
     * @BABOK Related: FR-PM-001 — delete a project
     */
    public function deleteProject(string $projectId): void;

    /**
     * @BABOK Related: FR-PM-002 — create a task
     */
    public function createTask(array $taskData): Task;

    /**
     * @BABOK Related: FR-PM-002 — read a task by id
     */
    public function getTask(string $taskId): Task;

    /**
     * @BABOK Related: FR-PM-002 — the tasks of a project
     */
    public function getProjectTasks(string $projectId): array;

    /**
     * @BABOK Related: FR-PM-002 — edit a task
     */
    public function updateTask(string $taskId, array $taskData): Task;

    /**
     * @BABOK Related: FR-PM-002 — delete a task
     */
    public function deleteTask(string $taskId): void;

    /**
     * @BABOK Related: FR-PM-004 — progress tracking on a task
     */
    public function updateTaskProgress(string $taskId, float $progress, string $status): void;

    /**
     * @BABOK Related: FR-PM-003 — allocate an employee to a project
     */
    public function assignEmployeeToProject(string $projectId, string $employeeId, array $assignmentData = []): void;

    /**
     * @BABOK Related: FR-PM-003 — end an allocation
     */
    public function removeEmployeeFromProject(string $projectId, string $employeeId): void;

    /**
     * @BABOK Related: FR-PM-003 — the allocated team of a project
     */
    public function getProjectTeam(string $projectId): array;
}