<?php
/**
 * ProjectService Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit;

use DateTime;
use ksfraser\CommonDb\Contract\DbConnectionInterface;
use Ksfraser\ProjectManagement\Contract\EmployeeServiceInterface;
use Ksfraser\ProjectManagement\Entity\Project;
use Ksfraser\ProjectManagement\Entity\Task;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use Ksfraser\ProjectManagement\Exception\ProjectNotFoundException;
use Ksfraser\ProjectManagement\Exception\TaskNotFoundException;
use Ksfraser\ProjectManagement\Exception\ValidationException;
use Ksfraser\ProjectManagement\ProjectService;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

/**
 * @BABOK Related: FR-PM-001, FR-PM-002, FR-PM-003, FR-PM-004 — tests the aggregate service
 */
class ProjectServiceTest extends TestCase
{
    /** @var DbConnectionInterface */
    private $db;
    /** @var EventDispatcherInterface */
    private $events;
    /** @var LoggerInterface */
    private $logger;
    /** @var EmployeeServiceInterface */
    private $employeeService;
    /** @var ProjectService */
    private $service;

    /**
     * @BABOK Related: FR-PM-001 — project aggregate service
     */
    protected function setUp(): void
    {
        $this->db = $this->createMock(DbConnectionInterface::class);
        $this->events = $this->createMock(EventDispatcherInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->employeeService = $this->createMock(EmployeeServiceInterface::class);

        $this->service = new ProjectService(
            $this->db,
            $this->events,
            $this->logger,
            $this->employeeService
        );
    }

    /**
     * @BABOK Related: FR-PM-001 — create a project
     */
    public function testCreateProjectSuccess(): void
    {
        $this->employeeService->method('employeeExists')->willReturn(true);

        $this->db->method('fetchAssoc')
            ->willReturnOnConsecutiveCalls(
                null,
                ['next_id' => '1']
            );
        $this->db->expects($this->once())->method('executeUpdate');

        $this->events->expects($this->once())->method('dispatch');

        $projectData = [
            'name' => 'New Project',
            'description' => 'Test Description',
            'startDate' => '2024-01-01',
            'projectManager' => 'mgr1',
            'budget' => 50000,
            'priority' => 'High',
            'status' => 'Active'
        ];

        $result = $this->service->createProject($projectData);

        $this->assertInstanceOf(Project::class, $result);
        $this->assertSame('New Project', $result->getName());
        $this->assertSame('Test Description', $result->getDescription());
        $this->assertSame(50000.0, $result->getBudget());
        $this->assertSame('High', $result->getPriority());
        $this->assertSame('Active', $result->getStatus());
    }

    /**
     * @BABOK Related: FR-PM-001 — project input guard
     */
    public function testCreateProjectValidationFailsOnEmptyName(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->createProject([
            'name' => '',
            'startDate' => '2024-01-01',
            'projectManager' => 'mgr1'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-001 — project input guard
     */
    public function testCreateProjectValidationFailsOnMissingStartDate(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->createProject([
            'name' => 'Test',
            'projectManager' => 'mgr1'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-001 — assign project manager (BR-PM-004)
     */
    public function testCreateProjectValidationFailsOnMissingManager(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->createProject([
            'name' => 'Test',
            'startDate' => '2024-01-01'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-001 — assign project manager (BR-PM-004)
     */
    public function testCreateProjectValidationFailsOnInvalidManager(): void
    {
        $this->expectException(ValidationException::class);

        $this->employeeService->method('employeeExists')->willReturn(false);

        $this->service->createProject([
            'name' => 'Test',
            'startDate' => '2024-01-01',
            'projectManager' => 'invalid'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-001 — project input guard
     */
    public function testCreateProjectValidationFailsOnEndDateBeforeStartDate(): void
    {
        $this->expectException(ValidationException::class);
        $this->employeeService->method('employeeExists')->willReturn(true);

        $this->service->createProject([
            'name' => 'Test',
            'startDate' => '2024-01-31',
            'endDate' => '2024-01-01',
            'projectManager' => 'mgr1'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-001 — read a project
     */
    public function testGetProjectSuccess(): void
    {
        $this->db->method('fetchAssoc')
            ->with("SELECT * FROM ksf_projectmanagement_projects WHERE project_id = ?", ['1'])
            ->willReturn([
                'project_id' => '1',
                'name' => 'Test Project',
                'description' => 'Test Desc',
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'budget' => '50000.00',
                'customer_id' => 'CUST001',
                'project_manager' => 'mgr1',
                'priority' => 'High',
                'status' => 'Active'
            ]);

        $result = $this->service->getProject('1');

        $this->assertInstanceOf(Project::class, $result);
        $this->assertSame('1', $result->getProjectId());
        $this->assertSame('Test Project', $result->getName());
        $this->assertSame(50000.0, $result->getBudget());
    }

    /**
     * @BABOK Related: FR-PM-001 — read a project
     */
    public function testGetProjectThrowsNotFoundException(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);

        $this->expectException(ProjectNotFoundException::class);
        $this->service->getProject('999');
    }

    /**
     * @BABOK Related: FR-PM-001 — edit a project
     */
    public function testUpdateProjectSuccess(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturnOnConsecutiveCalls(
                [
                    'project_id' => '1',
                    'name' => 'Old Name',
                    'description' => 'Old Desc',
                    'start_date' => '2024-01-01',
                    'project_manager' => 'mgr1',
                    'priority' => 'Low',
                    'status' => 'Planning'
                ],
                ['next_id' => '2']
            );

        $this->db->expects($this->once())->method('executeUpdate');
        $this->events->expects($this->once())->method('dispatch');

        $result = $this->service->updateProject('1', ['name' => 'New Name', 'priority' => 'High']);

        $this->assertSame('New Name', $result->getName());
        $this->assertSame('High', $result->getPriority());
    }

    /**
     * @BABOK Related: FR-PM-001 — delete a project
     */
    public function testDeleteProjectSuccess(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturn([
                'project_id' => '1',
                'name' => 'Test',
                'description' => '',
                'start_date' => '2024-01-01',
                'project_manager' => 'mgr1'
            ]);

        $this->db->expects($this->once())->method('executeUpdate');
        $this->logger->expects($this->once())->method('info');

        $this->service->deleteProject('1');
    }

    /**
     * @BABOK Related: FR-PM-002 — create a task
     */
    public function testCreateTaskSuccess(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturnOnConsecutiveCalls(
                [
                    'project_id' => '1',
                    'name' => 'Test Project',
                    'description' => '',
                    'start_date' => '2024-01-01',
                    'project_manager' => 'mgr1'
                ],
                ['next_id' => '1']
            );
        $this->db->expects($this->once())->method('executeUpdate');
        $this->events->expects($this->once())->method('dispatch');

        $result = $this->service->createTask([
            'projectId' => '1',
            'name' => 'New Task',
            'description' => 'Task Desc',
            'priority' => 'High'
        ]);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertSame('New Task', $result->getName());
        $this->assertSame('High', $result->getPriority());
    }

    /**
     * @BABOK Related: FR-PM-002 — task input guard
     */
    public function testCreateTaskValidationFailsOnMissingProjectId(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->createTask([
            'name' => 'Test Task'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-002 — task input guard
     */
    public function testCreateTaskValidationFailsOnMissingName(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->createTask([
            'projectId' => '1'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-002 — task assignment (BR-TASK-002)
     */
    public function testCreateTaskValidationFailsOnInvalidEmployee(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturn([
                'project_id' => '1',
                'name' => 'Test Project',
                'description' => '',
                'start_date' => '2024-01-01',
                'project_manager' => 'mgr1'
            ]);

        $this->employeeService->method('employeeExists')->willReturn(false);

        $this->expectException(ValidationException::class);

        $this->service->createTask([
            'projectId' => '1',
            'name' => 'Test Task',
            'assignedTo' => 'invalid'
        ]);
    }

    /**
     * @BABOK Related: FR-PM-002 — read a task
     */
    public function testGetTaskSuccess(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturn([
                'task_id' => '1',
                'project_id' => '1',
                'name' => 'Test Task',
                'description' => 'Test Desc',
                'assigned_to' => 'emp1',
                'parent_task_id' => '',
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-31',
                'estimated_hours' => '40',
                'actual_hours' => '20',
                'progress' => '50',
                'priority' => 'Medium',
                'status' => 'In Progress'
            ]);

        $result = $this->service->getTask('1');

        $this->assertInstanceOf(Task::class, $result);
        $this->assertSame('1', $result->getTaskId());
        $this->assertSame('Test Task', $result->getName());
        $this->assertSame(50.0, $result->getProgress());
    }

    /**
     * @BABOK Related: FR-PM-002 — read a task
     */
    public function testGetTaskThrowsNotFoundException(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);

        $this->expectException(TaskNotFoundException::class);
        $this->service->getTask('999');
    }

    /**
     * @BABOK Related: FR-PM-002 — the tasks of a project
     */
    public function testGetProjectTasksReturnsArray(): void
    {
        $this->db->method('fetchAll')
            ->willReturn([
                [
                    'task_id' => '1',
                    'project_id' => '1',
                    'name' => 'Task 1',
                    'description' => '',
                    'assigned_to' => '',
                    'parent_task_id' => '',
                    'priority' => 'Medium',
                    'status' => 'Not Started'
                ],
                [
                    'task_id' => '2',
                    'project_id' => '1',
                    'name' => 'Task 2',
                    'description' => '',
                    'assigned_to' => '',
                    'parent_task_id' => '1',
                    'priority' => 'High',
                    'status' => 'In Progress'
                ]
            ]);

        $result = $this->service->getProjectTasks('1');

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Task::class, $result[0]);
        $this->assertInstanceOf(Task::class, $result[1]);
        $this->assertSame('Task 1', $result[0]->getName());
        $this->assertSame('Task 2', $result[1]->getName());
    }

    /**
     * @BABOK Related: FR-PM-004 — track progress
     */
    public function testUpdateTaskProgressSuccess(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturn([
                'task_id' => '1',
                'project_id' => '1',
                'name' => 'Task',
                'description' => '',
                'assigned_to' => '',
                'progress' => '50',
                'actual_hours' => '20',
                'priority' => 'Medium',
                'status' => 'In Progress'
            ]);

        $this->db->expects($this->once())->method('executeUpdate');
        $this->events->expects($this->once())->method('dispatch');

        $this->service->updateTaskProgress('1', 75.0, 'In Progress');
    }

    /**
     * @BABOK Related: FR-PM-003 — allocate an employee
     */
    public function testAssignEmployeeToProjectSuccess(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturnOnConsecutiveCalls(
                [
                    'project_id' => '1',
                    'name' => 'Test Project',
                    'description' => '',
                    'start_date' => '2024-01-01',
                    'project_manager' => 'mgr1'
                ],
                ['count' => 0]
            );

        $this->employeeService->method('getEmployee')->willReturn(['employee_id' => 'emp1']);

        $this->db->expects($this->once())->method('executeUpdate');
        $this->events->expects($this->once())->method('dispatch');

        $this->service->assignEmployeeToProject('1', 'emp1', [
            'role' => 'Developer',
            'allocationPercentage' => 75.0
        ]);
    }

    /**
     * @BABOK Related: FR-PM-003 — guard against duplicate allocation
     */
    public function testAssignEmployeeToProjectFailsIfAlreadyAssigned(): void
    {
        $this->db->method('fetchAssoc')
            ->willReturnOnConsecutiveCalls(
                [
                    'project_id' => '1',
                    'name' => 'Test Project',
                    'description' => '',
                    'start_date' => '2024-01-01',
                    'project_manager' => 'mgr1'
                ],
                ['count' => 1]
            );

        $this->employeeService->method('getEmployee')->willReturn(['employee_id' => 'emp1']);

        $this->expectException(ValidationException::class);
        $this->service->assignEmployeeToProject('1', 'emp1');
    }

    /**
     * @BABOK Related: FR-PM-003 — end an allocation
     */
    public function testRemoveEmployeeFromProjectSuccess(): void
    {
        $this->db->expects($this->once())->method('executeUpdate');
        $this->logger->expects($this->once())->method('info');

        $this->service->removeEmployeeFromProject('1', 'emp1');
    }

    /**
     * @BABOK Related: FR-PM-003 — the allocated team
     */
    public function testGetProjectTeamReturnsArray(): void
    {
        $expectedTeam = [
            ['employee_id' => 'emp1', 'first_name' => 'John', 'last_name' => 'Doe'],
            ['employee_id' => 'emp2', 'first_name' => 'Jane', 'last_name' => 'Smith']
        ];

        $this->db->method('fetchAll')->willReturn($expectedTeam);

        $result = $this->service->getProjectTeam('1');

        $this->assertSame($expectedTeam, $result);
    }
}