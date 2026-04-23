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

interface ProjectServiceInterface
{
    public function createProject(array $projectData): Project;

    public function getProject(string $projectId): Project;

    public function updateProject(string $projectId, array $projectData): Project;

    public function deleteProject(string $projectId): void;

    public function createTask(array $taskData): Task;

    public function getTask(string $taskId): Task;

    public function getProjectTasks(string $projectId): array;

    public function updateTask(string $taskId, array $taskData): Task;

    public function deleteTask(string $taskId): void;

    public function updateTaskProgress(string $taskId, float $progress, string $status): void;

    public function assignEmployeeToProject(string $projectId, string $employeeId, array $assignmentData = []): void;

    public function removeEmployeeFromProject(string $projectId, string $employeeId): void;

    public function getProjectTeam(string $projectId): array;
}