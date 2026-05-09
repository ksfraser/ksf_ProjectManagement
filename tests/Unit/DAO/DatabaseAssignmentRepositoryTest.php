<?php
/**
 * DatabaseAssignmentRepository Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\DAO
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\DAO;

use DateTime;
use Ksfraser\ProjectManagement\Contract\DatabaseAdapterInterface;
use Ksfraser\ProjectManagement\DAO\DatabaseAssignmentRepository;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use PHPUnit\Framework\TestCase;

class DatabaseAssignmentRepositoryTest extends TestCase
{
    private DatabaseAdapterInterface $db;
    private DatabaseAssignmentRepository $repository;

    protected function setUp(): void
    {
        $this->db = $this->createMock(DatabaseAdapterInterface::class);
        $this->repository = new DatabaseAssignmentRepository($this->db);
    }

    public function testFindByProjectAndEmployeeReturnsAssignmentWhenFound(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'project_id' => 'proj1',
            'employee_id' => 'emp1',
            'role' => 'Developer',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'allocation_percentage' => '75.00'
        ]);

        $result = $this->repository->findByProjectAndEmployee('proj1', 'emp1');

        $this->assertInstanceOf(ProjectAssignment::class, $result);
        $this->assertSame('proj1', $result->getProjectId());
        $this->assertSame('emp1', $result->getEmployeeId());
        $this->assertSame('Developer', $result->getRole());
        $this->assertSame(75.0, $result->getAllocationPercentage());
    }

    public function testFindByProjectAndEmployeeReturnsNullWhenNotFound(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);

        $result = $this->repository->findByProjectAndEmployee('proj1', 'emp1');

        $this->assertNull($result);
    }

    public function testFindByProjectReturnsAllAssignments(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => 'proj1',
                'employee_id' => 'emp1',
                'role' => 'Developer',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'allocation_percentage' => '100.00'
            ],
            [
                'project_id' => 'proj1',
                'employee_id' => 'emp2',
                'role' => 'Manager',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'allocation_percentage' => '50.00'
            ]
        ]);

        $result = $this->repository->findByProject('proj1');

        $this->assertCount(2, $result);
    }

    public function testFindByEmployeeReturnsEmployeeAssignments(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => 'proj1',
                'employee_id' => 'emp1',
                'role' => 'Developer',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'allocation_percentage' => '100.00'
            ]
        ]);

        $result = $this->repository->findByEmployee('emp1');

        $this->assertCount(1, $result);
        $this->assertSame('emp1', $result[0]->getEmployeeId());
    }

    public function testFindActiveByProjectReturnsActiveAssignments(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => 'proj1',
                'employee_id' => 'emp1',
                'role' => 'Developer',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'allocation_percentage' => '100.00'
            ]
        ]);

        $result = $this->repository->findActiveByProject('proj1');

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->isActive());
    }

    public function testFindActiveByEmployeeReturnsActiveAssignments(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => 'proj1',
                'employee_id' => 'emp1',
                'role' => 'Developer',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'allocation_percentage' => '100.00'
            ]
        ]);

        $result = $this->repository->findActiveByEmployee('emp1');

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->isActive());
    }

    public function testSaveInsertsNewAssignment(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);
        $this->db->expects($this->once())->method('executeUpdate');

        $assignment = new ProjectAssignment('proj1', 'emp1', 'Developer', new DateTime('2024-01-01'), 100.0);

        $this->repository->save($assignment);
    }

    public function testSaveUpdatesExistingAssignment(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'project_id' => 'proj1',
            'employee_id' => 'emp1',
            'role' => 'Developer',
            'start_date' => '2024-01-01',
            'end_date' => null,
            'allocation_percentage' => '100.00'
        ]);
        $this->db->expects($this->once())->method('executeUpdate');

        $assignment = new ProjectAssignment('proj1', 'emp1', 'Lead Developer', new DateTime('2024-01-01'), 75.0);

        $this->repository->save($assignment);
    }

    public function testDeleteExecutesDeleteQuery(): void
    {
        $this->db->expects($this->once())->method('executeUpdate');

        $this->repository->delete('proj1', 'emp1');
    }
}