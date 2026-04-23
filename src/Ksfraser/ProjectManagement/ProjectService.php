<?php
/**
 * Project Service
 *
 * Handles project creation, task management, resource allocation, and progress tracking
 *
 * @package Ksfraser\ProjectManagement
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement;

use DateTime;
use Ksfraser\ProjectManagement\Contract\DatabaseAdapterInterface;
use Ksfraser\ProjectManagement\Contract\EmployeeServiceInterface;
use Ksfraser\ProjectManagement\Contract\ProjectServiceInterface;
use Ksfraser\ProjectManagement\Entity\Project;
use Ksfraser\ProjectManagement\Entity\Task;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use Ksfraser\ProjectManagement\Event\EmployeeAssignedToProjectEvent;
use Ksfraser\ProjectManagement\Event\ProjectCreatedEvent;
use Ksfraser\ProjectManagement\Event\ProjectUpdatedEvent;
use Ksfraser\ProjectManagement\Event\TaskCreatedEvent;
use Ksfraser\ProjectManagement\Event\TaskProgressUpdatedEvent;
use Ksfraser\ProjectManagement\Exception\ProjectNotFoundException;
use Ksfraser\ProjectManagement\Exception\TaskNotFoundException;
use Ksfraser\ProjectManagement\Exception\ValidationException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class ProjectService implements ProjectServiceInterface
{
    private const TABLE_PROJECTS = 'fa_pm_projects';
    private const TABLE_TASKS = 'fa_pm_tasks';
    private const TABLE_ASSIGNMENTS = 'fa_pm_assignments';

    public function __construct(
        private readonly DatabaseAdapterInterface $db,
        private readonly EventDispatcherInterface $events,
        private readonly LoggerInterface $logger,
        private readonly EmployeeServiceInterface $employeeService
    ) {
    }

    public function createProject(array $projectData): Project
    {
        $this->logger->info('Creating new project', ['name' => $projectData['name'] ?? '']);
        $this->validateProjectData($projectData);

        $projectId = $this->getNextProjectId();
        $startDate = new DateTime($projectData['startDate']);

        $project = new Project(
            $projectId,
            $projectData['name'],
            $projectData['description'] ?? '',
            $startDate,
            $projectData['projectManager']
        );

        if (isset($projectData['endDate'])) {
            $project->setEndDate(new DateTime($projectData['endDate']));
        }
        if (isset($projectData['budget'])) {
            $project->setBudget((float) $projectData['budget']);
        }
        if (isset($projectData['customerId'])) {
            $project->setCustomerId($projectData['customerId']);
        }
        if (isset($projectData['priority'])) {
            $project->setPriority($projectData['priority']);
        }
        if (isset($projectData['status'])) {
            $project->setStatus($projectData['status']);
        }

        $this->saveProject($project);
        $this->events->dispatch(new ProjectCreatedEvent($project));

        $this->logger->info('Project created successfully', ['projectId' => $projectId]);
        return $project;
    }

    public function getProject(string $projectId): Project
    {
        $sql = "SELECT * FROM " . self::TABLE_PROJECTS . " WHERE project_id = ?";
        $result = $this->db->fetchAssoc($sql, [$projectId]);

        if (!$result) {
            throw new ProjectNotFoundException($projectId);
        }

        return $this->hydrateProject($result);
    }

    public function updateProject(string $projectId, array $projectData): Project
    {
        $project = $this->getProject($projectId);
        $changedFields = [];

        if (isset($projectData['name'])) {
            $project->setName($projectData['name']);
            $changedFields[] = 'name';
        }
        if (isset($projectData['description'])) {
            $project->setDescription($projectData['description']);
            $changedFields[] = 'description';
        }
        if (isset($projectData['startDate'])) {
            $project->setStartDate(new DateTime($projectData['startDate']));
            $changedFields[] = 'startDate';
        }
        if (isset($projectData['endDate'])) {
            $project->setEndDate($projectData['endDate'] ? new DateTime($projectData['endDate']) : null);
            $changedFields[] = 'endDate';
        }
        if (isset($projectData['budget'])) {
            $project->setBudget((float) $projectData['budget']);
            $changedFields[] = 'budget';
        }
        if (isset($projectData['customerId'])) {
            $project->setCustomerId($projectData['customerId']);
            $changedFields[] = 'customerId';
        }
        if (isset($projectData['projectManager'])) {
            $project->setProjectManager($projectData['projectManager']);
            $changedFields[] = 'projectManager';
        }
        if (isset($projectData['priority'])) {
            $project->setPriority($projectData['priority']);
            $changedFields[] = 'priority';
        }
        if (isset($projectData['status'])) {
            $project->setStatus($projectData['status']);
            $changedFields[] = 'status';
        }

        $this->saveProject($project);
        $this->events->dispatch(new ProjectUpdatedEvent($project, $changedFields));

        $this->logger->info('Project updated', ['projectId' => $projectId, 'changed' => $changedFields]);
        return $project;
    }

    public function deleteProject(string $projectId): void
    {
        $this->getProject($projectId);
        $sql = "DELETE FROM " . self::TABLE_PROJECTS . " WHERE project_id = ?";
        $this->db->executeUpdate($sql, [$projectId]);
        $this->logger->info('Project deleted', ['projectId' => $projectId]);
    }

    public function createTask(array $taskData): Task
    {
        $this->logger->info('Creating project task', [
            'projectId' => $taskData['projectId'] ?? '',
            'name' => $taskData['name'] ?? ''
        ]);
        $this->validateTaskData($taskData);

        $taskId = $this->getNextTaskId();

        $task = new Task(
            $taskId,
            $taskData['projectId'],
            $taskData['name'],
            $taskData['description'] ?? '',
            $taskData['assignedTo'] ?? ''
        );

        if (isset($taskData['parentTaskId'])) {
            $task->setParentTaskId($taskData['parentTaskId']);
        }
        if (isset($taskData['startDate'])) {
            $task->setStartDate(new DateTime($taskData['startDate']));
        }
        if (isset($taskData['endDate'])) {
            $task->setEndDate(new DateTime($taskData['endDate']));
        }
        if (isset($taskData['estimatedHours'])) {
            $task->setEstimatedHours((float) $taskData['estimatedHours']);
        }
        if (isset($taskData['priority'])) {
            $task->setPriority($taskData['priority']);
        }
        if (isset($taskData['status'])) {
            $task->setStatus($taskData['status']);
        }

        $this->saveTask($task);
        $this->events->dispatch(new TaskCreatedEvent($task));

        $this->logger->info('Task created successfully', ['taskId' => $taskId]);
        return $task;
    }

    public function getTask(string $taskId): Task
    {
        $sql = "SELECT * FROM " . self::TABLE_TASKS . " WHERE task_id = ?";
        $result = $this->db->fetchAssoc($sql, [$taskId]);

        if (!$result) {
            throw new TaskNotFoundException($taskId);
        }

        return $this->hydrateTask($result);
    }

    public function getProjectTasks(string $projectId): array
    {
        $sql = "SELECT * FROM " . self::TABLE_TASKS . " WHERE project_id = ? ORDER BY parent_task_id, task_id";
        $results = $this->db->fetchAll($sql, [$projectId]);

        $tasks = [];
        foreach ($results as $result) {
            $tasks[] = $this->hydrateTask($result);
        }
        return $tasks;
    }

    public function updateTask(string $taskId, array $taskData): Task
    {
        $task = $this->getTask($taskId);

        if (isset($taskData['name'])) {
            $task->setName($taskData['name']);
        }
        if (isset($taskData['description'])) {
            $task->setDescription($taskData['description']);
        }
        if (isset($taskData['assignedTo'])) {
            $task->setAssignedTo($taskData['assignedTo']);
        }
        if (isset($taskData['parentTaskId'])) {
            $task->setParentTaskId($taskData['parentTaskId']);
        }
        if (isset($taskData['startDate'])) {
            $task->setStartDate($taskData['startDate'] ? new DateTime($taskData['startDate']) : null);
        }
        if (isset($taskData['endDate'])) {
            $task->setEndDate($taskData['endDate'] ? new DateTime($taskData['endDate']) : null);
        }
        if (isset($taskData['estimatedHours'])) {
            $task->setEstimatedHours((float) $taskData['estimatedHours']);
        }
        if (isset($taskData['actualHours'])) {
            $task->setActualHours((float) $taskData['actualHours']);
        }
        if (isset($taskData['progress'])) {
            $task->setProgress((float) $taskData['progress']);
        }
        if (isset($taskData['priority'])) {
            $task->setPriority($taskData['priority']);
        }
        if (isset($taskData['status'])) {
            $task->setStatus($taskData['status']);
        }

        $this->saveTask($task);
        $this->logger->info('Task updated', ['taskId' => $taskId]);
        return $task;
    }

    public function deleteTask(string $taskId): void
    {
        $this->getTask($taskId);
        $sql = "DELETE FROM " . self::TABLE_TASKS . " WHERE task_id = ?";
        $this->db->executeUpdate($sql, [$taskId]);
        $this->logger->info('Task deleted', ['taskId' => $taskId]);
    }

    public function updateTaskProgress(string $taskId, float $progress, string $status): void
    {
        $this->logger->info('Updating task progress', [
            'taskId' => $taskId,
            'progress' => $progress,
            'status' => $status
        ]);

        $task = $this->getTask($taskId);
        $previousProgress = $task->getProgress();

        $task->setProgress($progress);
        $task->setStatus($status);

        $sql = "UPDATE " . self::TABLE_TASKS . " SET progress = ?, status = ?, actual_hours = ? WHERE task_id = ?";
        $this->db->executeUpdate($sql, [$task->getProgress(), $task->getStatus(), $task->getActualHours(), $taskId]);

        $this->events->dispatch(new TaskProgressUpdatedEvent($task, $previousProgress, $progress));
    }

    public function assignEmployeeToProject(string $projectId, string $employeeId, array $assignmentData = []): void
    {
        $this->logger->info('Assigning employee to project', ['projectId' => $projectId, 'employeeId' => $employeeId]);

        $this->getProject($projectId);
        $this->employeeService->getEmployee($employeeId);

        if ($this->isEmployeeAssignedToProject($employeeId, $projectId)) {
            throw new ValidationException('Employee is already assigned to this project');
        }

        $assignment = new ProjectAssignment(
            $projectId,
            $employeeId,
            $assignmentData['role'] ?? 'Team Member',
            new DateTime($assignmentData['startDate'] ?? 'now'),
            (float) ($assignmentData['allocationPercentage'] ?? 100.0)
        );

        if (isset($assignmentData['endDate'])) {
            $assignment->setEndDate(new DateTime($assignmentData['endDate']));
        }

        $this->saveProjectAssignment($assignment);
        $this->events->dispatch(new EmployeeAssignedToProjectEvent($assignment));
    }

    public function removeEmployeeFromProject(string $projectId, string $employeeId): void
    {
        $sql = "DELETE FROM " . self::TABLE_ASSIGNMENTS . " WHERE project_id = ? AND employee_id = ?";
        $this->db->executeUpdate($sql, [$projectId, $employeeId]);
        $this->logger->info('Employee removed from project', ['projectId' => $projectId, 'employeeId' => $employeeId]);
    }

    public function getProjectTeam(string $projectId): array
    {
        $sql = "SELECT pa.*, e.first_name, e.last_name, e.email, e.job_title
                FROM " . self::TABLE_ASSIGNMENTS . " pa
                INNER JOIN employees e ON pa.employee_id = e.employee_id
                WHERE pa.project_id = ?
                AND (pa.end_date IS NULL OR pa.end_date >= CURDATE())
                ORDER BY e.last_name, e.first_name";

        return $this->db->fetchAll($sql, [$projectId]);
    }

    private function validateProjectData(array $data): void
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Project name is required';
        }
        if (empty($data['startDate'])) {
            $errors['startDate'] = 'Start date is required';
        }
        if (empty($data['projectManager'])) {
            $errors['projectManager'] = 'Project manager is required';
        }

        if (!empty($data['projectManager']) && !$this->employeeService->employeeExists($data['projectManager'])) {
            $errors['projectManager'] = 'Project manager does not exist';
        }

        if (!empty($data['startDate']) && !empty($data['endDate'])) {
            $startDate = new DateTime($data['startDate']);
            $endDate = new DateTime($data['endDate']);
            if ($endDate < $startDate) {
                $errors['endDate'] = 'End date cannot be before start date';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException('Project validation failed', $errors);
        }
    }

    private function validateTaskData(array $data): void
    {
        $errors = [];

        if (empty($data['projectId'])) {
            $errors['projectId'] = 'Project ID is required';
        }
        if (empty($data['name'])) {
            $errors['name'] = 'Task name is required';
        }

        if (!empty($data['projectId'])) {
            try {
                $this->getProject($data['projectId']);
            } catch (ProjectNotFoundException $e) {
                $errors['projectId'] = 'Project does not exist';
            }
        }

        if (!empty($data['assignedTo']) && !$this->employeeService->employeeExists($data['assignedTo'])) {
            $errors['assignedTo'] = 'Assigned employee does not exist';
        }

        if (!empty($data['parentTaskId'])) {
            try {
                $this->getTask($data['parentTaskId']);
            } catch (TaskNotFoundException $e) {
                $errors['parentTaskId'] = 'Parent task does not exist';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException('Task validation failed', $errors);
        }
    }

    private function isEmployeeAssignedToProject(string $employeeId, string $projectId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM " . self::TABLE_ASSIGNMENTS . "
                WHERE employee_id = ? AND project_id = ?
                AND (end_date IS NULL OR end_date >= CURDATE())";

        $result = $this->db->fetchAssoc($sql, [$employeeId, $projectId]);
        return ($result['count'] ?? 0) > 0;
    }

    private function getNextProjectId(): string
    {
        $sql = "SELECT MAX(CAST(project_id AS UNSIGNED)) + 1 as next_id FROM " . self::TABLE_PROJECTS;
        $result = $this->db->fetchAssoc($sql);
        return (string) ($result['next_id'] ?? 1);
    }

    private function getNextTaskId(): string
    {
        $sql = "SELECT MAX(CAST(task_id AS UNSIGNED)) + 1 as next_id FROM " . self::TABLE_TASKS;
        $result = $this->db->fetchAssoc($sql);
        return (string) ($result['next_id'] ?? 1);
    }

    private function saveProject(Project $project): void
    {
        $sql = "INSERT INTO " . self::TABLE_PROJECTS . " (
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

    private function saveTask(Task $task): void
    {
        $sql = "INSERT INTO " . self::TABLE_TASKS . " (
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
            $task->getStartDate()?->format('Y-m-d'),
            $task->getEndDate()?->format('Y-m-d'),
            $task->getEstimatedHours(),
            $task->getActualHours(),
            $task->getProgress(),
            $task->getPriority(),
            $task->getStatus()
        ]);
    }

    private function saveProjectAssignment(ProjectAssignment $assignment): void
    {
        $sql = "INSERT INTO " . self::TABLE_ASSIGNMENTS . " (
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

    private function hydrateProject(array $row): Project
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

    private function hydrateTask(array $row): Task
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