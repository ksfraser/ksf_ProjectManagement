<?php
/**
 * FrontAccounting Project Management Module
 *
 * Comprehensive project management system with dotProject/OpenProject-like capabilities.
 *
 * @package FA\Modules\ProjectManagement
 * @version 1.0.0
 * @author FrontAccounting Team
 * @license GPL-3.0
 */

namespace FA\Modules\ProjectManagement;

use FA\Events\EventDispatcherInterface;
use FA\Database\DBALInterface;
use FA\Modules\EmployeeManagement\EmployeeService;
use Psr\Log\LoggerInterface;

/**
 * Project Service
 *
 * Handles project creation, task management, resource allocation, and progress tracking
 */
class ProjectService
{
    private DBALInterface $db;
    private EventDispatcherInterface $events;
    private LoggerInterface $logger;
    private EmployeeService $employeeService;

    public function __construct(
        DBALInterface $db,
        EventDispatcherInterface $events,
        LoggerInterface $logger,
        EmployeeService $employeeService
    ) {
        $this->db = $db;
        $this->events = $events;
        $this->logger = $logger;
        $this->employeeService = $employeeService;
    }

    /**
     * Create a new project
     *
     * @param array $projectData Project information
     * @return Project The created project
     * @throws ProjectException
     */
    public function createProject(array $projectData): Project
    {
        $this->logger->info('Creating new project', ['name' => $projectData['name'] ?? '']);

        $this->validateProjectData($projectData);

        $projectId = $this->getNextProjectId();

        $project = new Project(
            $projectId,
            $projectData['name'],
            $projectData['description'] ?? '',
            new \DateTime($projectData['startDate']),
            $projectData['projectManager']
        );

        // Set optional fields
        if (isset($projectData['endDate'])) {
            $project->setEndDate(new \DateTime($projectData['endDate']));
        }
        if (isset($projectData['budget'])) {
            $project->setBudget((float)$projectData['budget']);
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

        // Save to database
        $this->saveProject($project);

        $this->events->dispatch(new ProjectCreatedEvent($project));

        $this->logger->info('Project created successfully', ['projectId' => $projectId]);

        return $project;
    }

    /**
     * Create a project task
     *
     * @param array $taskData Task information
     * @return Task The created task
     * @throws ProjectException
     */
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

        // Set optional fields
        if (isset($taskData['parentTaskId'])) {
            $task->setParentTaskId($taskData['parentTaskId']);
        }
        if (isset($taskData['startDate'])) {
            $task->setStartDate(new \DateTime($taskData['startDate']));
        }
        if (isset($taskData['endDate'])) {
            $task->setEndDate(new \DateTime($taskData['endDate']));
        }
        if (isset($taskData['estimatedHours'])) {
            $task->setEstimatedHours((float)$taskData['estimatedHours']);
        }
        if (isset($taskData['priority'])) {
            $task->setPriority($taskData['priority']);
        }
        if (isset($taskData['status'])) {
            $task->setStatus($taskData['status']);
        }

        // Save to database
        $this->saveTask($task);

        $this->events->dispatch(new TaskCreatedEvent($task));

        $this->logger->info('Task created successfully', ['taskId' => $taskId]);

        return $task;
    }

    /**
     * Assign employee to project
     *
     * @param string $projectId Project ID
     * @param string $employeeId Employee ID
     * @param array $assignmentData Assignment details
     * @throws ProjectException
     */
    public function assignEmployeeToProject(string $projectId, string $employeeId, array $assignmentData = []): void
    {
        $this->logger->info('Assigning employee to project', [
            'projectId' => $projectId,
            'employeeId' => $employeeId
        ]);

        // Validate project and employee exist
        $this->getProject($projectId);
        $this->employeeService->getEmployee($employeeId);

        // Check if already assigned
        if ($this->isEmployeeAssignedToProject($employeeId, $projectId)) {
            throw new ProjectException("Employee is already assigned to this project");
        }

        $assignment = new ProjectAssignment(
            $projectId,
            $employeeId,
            $assignmentData['role'] ?? 'Team Member',
            $assignmentData['startDate'] ?? new \DateTime(),
            (float)($assignmentData['allocationPercentage'] ?? 100.0)
        );

        if (isset($assignmentData['endDate'])) {
            $assignment->setEndDate(new \DateTime($assignmentData['endDate']));
        }

        $this->saveProjectAssignment($assignment);

        $this->events->dispatch(new EmployeeAssignedToProjectEvent($assignment));
    }

    /**
     * Update task progress
     *
     * @param string $taskId Task ID
     * @param float $progress Progress percentage (0-100)
     * @param string $status New status
     * @throws ProjectException
     */
    public function updateTaskProgress(string $taskId, float $progress, string $status): void
    {
        $this->logger->info('Updating task progress', [
            'taskId' => $taskId,
            'progress' => $progress,
            'status' => $status
        ]);

        $task = $this->getTask($taskId);

        $task->setProgress($progress);
        $task->setStatus($status);

        $this->updateTask($task);

        $this->events->dispatch(new TaskProgressUpdatedEvent($task));
    }

    /**
     * Get project by ID
     *
     * @param string $projectId Project ID
     * @return Project
     * @throws ProjectException
     */
    public function getProject(string $projectId): Project
    {
        $sql = "SELECT * FROM projects WHERE project_id = ?";
        $result = $this->db->fetchAssoc($sql, [$projectId]);

        if (!$result) {
            throw new ProjectException("Project {$projectId} not found");
        }

        $project = new Project(
            $result['project_id'],
            $result['name'],
            $result['description'],
            new \DateTime($result['start_date']),
            $result['project_manager']
        );

        if ($result['end_date']) {
            $project->setEndDate(new \DateTime($result['end_date']));
        }
        $project->setBudget((float)($result['budget'] ?? 0));
        $project->setCustomerId($result['customer_id'] ?? '');
        $project->setPriority($result['priority'] ?? 'Medium');
        $project->setStatus($result['status'] ?? 'Planning');

        return $project;
    }

    /**
     * Get task by ID
     *
     * @param string $taskId Task ID
     * @return Task
     * @throws ProjectException
     */
    public function getTask(string $taskId): Task
    {
        $sql = "SELECT * FROM project_tasks WHERE task_id = ?";
        $result = $this->db->fetchAssoc($sql, [$taskId]);

        if (!$result) {
            throw new ProjectException("Task {$taskId} not found");
        }

        $task = new Task(
            $result['task_id'],
            $result['project_id'],
            $result['name'],
            $result['description'],
            $result['assigned_to']
        );

        $task->setParentTaskId($result['parent_task_id'] ?? '');
        if ($result['start_date']) {
            $task->setStartDate(new \DateTime($result['start_date']));
        }
        if ($result['end_date']) {
            $task->setEndDate(new \DateTime($result['end_date']));
        }
        $task->setEstimatedHours((float)($result['estimated_hours'] ?? 0));
        $task->setActualHours((float)($result['actual_hours'] ?? 0));
        $task->setProgress((float)($result['progress'] ?? 0));
        $task->setPriority($result['priority'] ?? 'Medium');
        $task->setStatus($result['status'] ?? 'Not Started');

        return $task;
    }

    /**
     * Get project tasks
     *
     * @param string $projectId Project ID
     * @return Task[]
     */
    public function getProjectTasks(string $projectId): array
    {
        $sql = "SELECT * FROM project_tasks WHERE project_id = ? ORDER BY parent_task_id, task_id";
        $results = $this->db->fetchAll($sql, [$projectId]);

        $tasks = [];
        foreach ($results as $result) {
            $task = new Task(
                $result['task_id'],
                $result['project_id'],
                $result['name'],
                $result['description'],
                $result['assigned_to']
            );

            $task->setParentTaskId($result['parent_task_id'] ?? '');
            if ($result['start_date']) {
                $task->setStartDate(new \DateTime($result['start_date']));
            }
            if ($result['end_date']) {
                $task->setEndDate(new \DateTime($result['end_date']));
            }
            $task->setEstimatedHours((float)($result['estimated_hours'] ?? 0));
            $task->setActualHours((float)($result['actual_hours'] ?? 0));
            $task->setProgress((float)($result['progress'] ?? 0));
            $task->setPriority($result['priority'] ?? 'Medium');
            $task->setStatus($result['status'] ?? 'Not Started');

            $tasks[] = $task;
        }

        return $tasks;
    }

    /**
     * Get project team members
     *
     * @param string $projectId Project ID
     * @return array Team member data
     */
    public function getProjectTeam(string $projectId): array
    {
        $sql = "SELECT pa.*, e.first_name, e.last_name, e.email, e.job_title
                FROM project_assignments pa
                INNER JOIN employees e ON pa.employee_id = e.employee_id
                WHERE pa.project_id = ?
                AND (pa.end_date IS NULL OR pa.end_date >= CURDATE())
                ORDER BY e.last_name, e.first_name";

        return $this->db->fetchAll($sql, [$projectId]);
    }

    /**
     * Validate project data
     *
     * @param array $data
     * @throws ProjectException
     */
    private function validateProjectData(array $data): void
    {
        if (empty($data['name'])) {
            throw new ProjectException("Project name is required");
        }

        if (empty($data['startDate'])) {
            throw new ProjectException("Start date is required");
        }

        if (empty($data['projectManager'])) {
            throw new ProjectException("Project manager is required");
        }

        // Validate project manager exists
        $this->employeeService->getEmployee($data['projectManager']);

        // Validate date logic
        $startDate = new \DateTime($data['startDate']);
        if (isset($data['endDate'])) {
            $endDate = new \DateTime($data['endDate']);
            if ($endDate < $startDate) {
                throw new ProjectException("End date cannot be before start date");
            }
        }
    }

    /**
     * Validate task data
     *
     * @param array $data
     * @throws ProjectException
     */
    private function validateTaskData(array $data): void
    {
        if (empty($data['projectId'])) {
            throw new ProjectException("Project ID is required");
        }

        if (empty($data['name'])) {
            throw new ProjectException("Task name is required");
        }

        // Validate project exists
        $this->getProject($data['projectId']);

        // Validate assigned employee if provided
        if (!empty($data['assignedTo'])) {
            $this->employeeService->getEmployee($data['assignedTo']);
        }

        // Validate parent task if provided
        if (!empty($data['parentTaskId'])) {
            $this->getTask($data['parentTaskId']);
        }
    }

    /**
     * Check if employee is assigned to project
     *
     * @param string $employeeId
     * @param string $projectId
     * @return bool
     */
    private function isEmployeeAssignedToProject(string $employeeId, string $projectId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM project_assignments
                WHERE employee_id = ? AND project_id = ?
                AND (end_date IS NULL OR end_date >= CURDATE())";

        $result = $this->db->fetchAssoc($sql, [$employeeId, $projectId]);

        return $result['count'] > 0;
    }

    /**
     * Get next project ID
     *
     * @return string
     */
    private function getNextProjectId(): string
    {
        $sql = "SELECT MAX(CAST(project_id AS UNSIGNED)) + 1 as next_id FROM projects";
        $result = $this->db->fetchAssoc($sql);

        return (string)($result['next_id'] ?? 1);
    }

    /**
     * Get next task ID
     *
     * @return string
     */
    private function getNextTaskId(): string
    {
        $sql = "SELECT MAX(CAST(task_id AS UNSIGNED)) + 1 as next_id FROM project_tasks";
        $result = $this->db->fetchAssoc($sql);

        return (string)($result['next_id'] ?? 1);
    }

    /**
     * Save project to database
     *
     * @param Project $project
     */
    private function saveProject(Project $project): void
    {
        $sql = "INSERT INTO projects (
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

    /**
     * Save task to database
     *
     * @param Task $task
     */
    private function saveTask(Task $task): void
    {
        $sql = "INSERT INTO project_tasks (
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

    /**
     * Save project assignment
     *
     * @param ProjectAssignment $assignment
     */
    private function saveProjectAssignment(ProjectAssignment $assignment): void
    {
        $sql = "INSERT INTO project_assignments (
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

    /**
     * Update task in database
     *
     * @param Task $task
     */
    private function updateTask(Task $task): void
    {
        $sql = "UPDATE project_tasks SET
                    progress = ?, status = ?, actual_hours = ?
                WHERE task_id = ?";

        $this->db->executeUpdate($sql, [
            $task->getProgress(),
            $task->getStatus(),
            $task->getActualHours(),
            $task->getTaskId()
        ]);
    }
}