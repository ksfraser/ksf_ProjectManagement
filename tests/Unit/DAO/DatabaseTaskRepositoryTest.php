<?php
/**
 * DatabaseTaskRepository Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\DAO
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\DAO;

use DateTime;
use Ksfraser\ProjectManagement\Contract\DatabaseAdapterInterface;
use Ksfraser\ProjectManagement\DAO\DatabaseTaskRepository;
use Ksfraser\ProjectManagement\Entity\Task;
use PHPUnit\Framework\TestCase;

class DatabaseTaskRepositoryTest extends TestCase
{
    private DatabaseAdapterInterface $db;
    private DatabaseTaskRepository $repository;

    protected function setUp(): void
    {
        $this->db = $this->createMock(DatabaseAdapterInterface::class);
        $this->repository = new DatabaseTaskRepository($this->db);
    }

    public function testFindReturnsTaskWhenFound(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'task_id' => '1',
            'project_id' => 'proj1',
            'name' => 'Test Task',
            'description' => 'Test Description',
            'assigned_to' => 'emp1',
            'parent_task_id' => '',
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'estimated_hours' => '40.00',
            'actual_hours' => '20.00',
            'progress' => '50.00',
            'priority' => 'High',
            'status' => 'In Progress'
        ]);

        $result = $this->repository->find('1');

        $this->assertInstanceOf(Task::class, $result);
        $this->assertSame('1', $result->getTaskId());
        $this->assertSame('Test Task', $result->getName());
        $this->assertSame(50.0, $result->getProgress());
    }

    public function testFindReturnsNullWhenNotFound(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);

        $result = $this->repository->find('999');

        $this->assertNull($result);
    }

    public function testFindByProjectReturnsTasksForProject(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'task_id' => '1',
                'project_id' => 'proj1',
                'name' => 'Task 1',
                'description' => '',
                'assigned_to' => '',
                'parent_task_id' => '',
                'priority' => 'Medium',
                'status' => 'Not Started'
            ],
            [
                'task_id' => '2',
                'project_id' => 'proj1',
                'name' => 'Task 2',
                'description' => '',
                'assigned_to' => '',
                'parent_task_id' => '',
                'priority' => 'High',
                'status' => 'In Progress'
            ]
        ]);

        $result = $this->repository->findByProject('proj1');

        $this->assertCount(2, $result);
    }

    public function testFindByParentReturnsChildTasks(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'task_id' => '2',
                'project_id' => 'proj1',
                'name' => 'Child Task',
                'description' => '',
                'assigned_to' => '',
                'parent_task_id' => '1',
                'priority' => 'Medium',
                'status' => 'Not Started'
            ]
        ]);

        $result = $this->repository->findByParent('1');

        $this->assertCount(1, $result);
        $this->assertSame('1', $result[0]->getParentTaskId());
    }

    public function testFindByAssigneeReturnsTasksForEmployee(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'task_id' => '1',
                'project_id' => 'proj1',
                'name' => 'My Task',
                'description' => '',
                'assigned_to' => 'emp1',
                'parent_task_id' => '',
                'priority' => 'Medium',
                'status' => 'In Progress'
            ]
        ]);

        $result = $this->repository->findByAssignee('emp1');

        $this->assertCount(1, $result);
        $this->assertSame('emp1', $result[0]->getAssignedTo());
    }

    public function testFindOverdueReturnsOverdueTasks(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'task_id' => '1',
                'project_id' => 'proj1',
                'name' => 'Overdue Task',
                'description' => '',
                'assigned_to' => '',
                'parent_task_id' => '',
                'start_date' => '2024-01-01',
                'end_date' => '2020-01-01',
                'priority' => 'Medium',
                'status' => 'In Progress'
            ]
        ]);

        $result = $this->repository->findOverdue();

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->isOverdue());
    }

    public function testFindByStatusReturnsFilteredTasks(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'task_id' => '1',
                'project_id' => 'proj1',
                'name' => 'Completed Task',
                'description' => '',
                'assigned_to' => '',
                'parent_task_id' => '',
                'progress' => '100.00',
                'priority' => 'Medium',
                'status' => 'Completed'
            ]
        ]);

        $result = $this->repository->findByStatus('Completed');

        $this->assertCount(1, $result);
        $this->assertSame('Completed', $result[0]->getStatus());
    }

    public function testCountByProjectReturnsCount(): void
    {
        $this->db->method('fetchAssoc')->willReturn(['cnt' => '5']);

        $result = $this->repository->countByProject('proj1');

        $this->assertSame(5, $result);
    }

    public function testCountCompletedByProjectReturnsCount(): void
    {
        $this->db->method('fetchAssoc')->willReturn(['cnt' => '3']);

        $result = $this->repository->countCompletedByProject('proj1');

        $this->assertSame(3, $result);
    }

    public function testSaveInsertsNewTask(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);
        $this->db->expects($this->once())->method('executeUpdate');

        $task = new Task('1', 'proj1', 'New Task', 'Description');

        $this->repository->save($task);
    }

    public function testSaveUpdatesExistingTask(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'task_id' => '1',
            'project_id' => 'proj1',
            'name' => 'Existing',
            'description' => '',
            'assigned_to' => '',
            'parent_task_id' => '',
            'priority' => 'Medium',
            'status' => 'Not Started'
        ]);
        $this->db->expects($this->once())->method('executeUpdate');

        $task = new Task('1', 'proj1', 'Updated', 'Description');

        $this->repository->save($task);
    }

    public function testDeleteExecutesDeleteQuery(): void
    {
        $this->db->expects($this->once())->method('executeUpdate');

        $this->repository->delete('1');
    }

    public function testHydrationWithNullDates(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'task_id' => '1',
            'project_id' => 'proj1',
            'name' => 'No Dates',
            'description' => '',
            'assigned_to' => '',
            'parent_task_id' => '',
            'start_date' => null,
            'end_date' => null,
            'estimated_hours' => '0.00',
            'actual_hours' => '0.00',
            'progress' => '0.00',
            'priority' => 'Medium',
            'status' => 'Not Started'
        ]);

        $result = $this->repository->find('1');

        $this->assertNull($result->getStartDate());
        $this->assertNull($result->getEndDate());
    }
}