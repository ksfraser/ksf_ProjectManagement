<?php
/**
 * DatabaseProjectRepository Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\DAO
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\DAO;

use DateTime;
use Ksfraser\ProjectManagement\Contract\DatabaseAdapterInterface;
use Ksfraser\ProjectManagement\DAO\DatabaseProjectRepository;
use Ksfraser\ProjectManagement\Entity\Project;
use PHPUnit\Framework\TestCase;

class DatabaseProjectRepositoryTest extends TestCase
{
    private DatabaseAdapterInterface $db;
    private DatabaseProjectRepository $repository;

    protected function setUp(): void
    {
        $this->db = $this->createMock(DatabaseAdapterInterface::class);
        $this->repository = new DatabaseProjectRepository($this->db);
    }

    public function testFindReturnsProjectWhenFound(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'project_id' => '1',
            'name' => 'Test Project',
            'description' => 'Test Description',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'budget' => '50000.00',
            'customer_id' => 'CUST001',
            'project_manager' => 'mgr1',
            'priority' => 'High',
            'status' => 'Active'
        ]);

        $result = $this->repository->find('1');

        $this->assertInstanceOf(Project::class, $result);
        $this->assertSame('1', $result->getProjectId());
        $this->assertSame('Test Project', $result->getName());
        $this->assertSame(50000.0, $result->getBudget());
    }

    public function testFindReturnsNullWhenNotFound(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);

        $result = $this->repository->find('999');

        $this->assertNull($result);
    }

    public function testFindAllReturnsArrayOfProjects(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => '1',
                'name' => 'Project 1',
                'description' => '',
                'start_date' => '2024-01-01',
                'project_manager' => 'mgr1',
                'priority' => 'Medium',
                'status' => 'Planning'
            ],
            [
                'project_id' => '2',
                'name' => 'Project 2',
                'description' => '',
                'start_date' => '2024-02-01',
                'project_manager' => 'mgr1',
                'priority' => 'High',
                'status' => 'Active'
            ]
        ]);

        $result = $this->repository->findAll();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Project::class, $result[0]);
        $this->assertInstanceOf(Project::class, $result[1]);
    }

    public function testFindByStatusReturnsFilteredProjects(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => '1',
                'name' => 'Active Project',
                'description' => '',
                'start_date' => '2024-01-01',
                'project_manager' => 'mgr1',
                'priority' => 'Medium',
                'status' => 'Active'
            ]
        ]);

        $result = $this->repository->findByStatus('Active');

        $this->assertCount(1, $result);
        $this->assertSame('Active', $result[0]->getStatus());
    }

    public function testFindByCustomerReturnsCustomerProjects(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => '1',
                'name' => 'Customer Project',
                'description' => '',
                'start_date' => '2024-01-01',
                'project_manager' => 'mgr1',
                'customer_id' => 'CUST001',
                'priority' => 'Medium',
                'status' => 'Active'
            ]
        ]);

        $result = $this->repository->findByCustomer('CUST001');

        $this->assertCount(1, $result);
        $this->assertSame('CUST001', $result[0]->getCustomerId());
    }

    public function testFindByManagerReturnsManagerProjects(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => '1',
                'name' => 'Managed Project',
                'description' => '',
                'start_date' => '2024-01-01',
                'project_manager' => 'mgr1',
                'priority' => 'Medium',
                'status' => 'Active'
            ]
        ]);

        $result = $this->repository->findByManager('mgr1');

        $this->assertCount(1, $result);
        $this->assertSame('mgr1', $result[0]->getProjectManager());
    }

    public function testFindOverdueReturnsOverdueProjects(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => '1',
                'name' => 'Overdue Project',
                'description' => '',
                'start_date' => '2024-01-01',
                'end_date' => '2020-01-01',
                'project_manager' => 'mgr1',
                'priority' => 'Medium',
                'status' => 'Active'
            ]
        ]);

        $result = $this->repository->findOverdue();

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->isOverdue());
    }

    public function testFindActiveReturnsActiveProjects(): void
    {
        $this->db->method('fetchAll')->willReturn([
            [
                'project_id' => '1',
                'name' => 'Active Project',
                'description' => '',
                'start_date' => '2024-01-01',
                'end_date' => '2030-01-01',
                'project_manager' => 'mgr1',
                'priority' => 'Medium',
                'status' => 'Active'
            ]
        ]);

        $result = $this->repository->findActive();

        $this->assertCount(1, $result);
    }

    public function testCountReturnsTotalCount(): void
    {
        $this->db->method('fetchAssoc')->willReturn(['cnt' => '10']);

        $result = $this->repository->count();

        $this->assertSame(10, $result);
    }

    public function testCountByStatusReturnsFilteredCount(): void
    {
        $this->db->method('fetchAssoc')->willReturn(['cnt' => '5']);

        $result = $this->repository->countByStatus('Active');

        $this->assertSame(5, $result);
    }

    public function testSaveInsertsNewProject(): void
    {
        $this->db->method('fetchAssoc')->willReturn(null);
        $this->db->expects($this->once())->method('executeUpdate');

        $project = new Project('1', 'New Project', 'Desc', new DateTime('2024-01-01'), 'mgr1');

        $this->repository->save($project);
    }

    public function testSaveUpdatesExistingProject(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'project_id' => '1',
            'name' => 'Existing',
            'description' => '',
            'start_date' => '2024-01-01',
            'project_manager' => 'mgr1',
            'priority' => 'Medium',
            'status' => 'Active'
        ]);
        $this->db->expects($this->once())->method('executeUpdate');

        $project = new Project('1', 'Updated', 'Desc', new DateTime('2024-01-01'), 'mgr1');

        $this->repository->save($project);
    }

    public function testDeleteExecutesDeleteQuery(): void
    {
        $this->db->expects($this->once())->method('executeUpdate');

        $this->repository->delete('1');
    }

    public function testHydrationWithNullEndDate(): void
    {
        $this->db->method('fetchAssoc')->willReturn([
            'project_id' => '1',
            'name' => 'No End Date',
            'description' => '',
            'start_date' => '2024-01-01',
            'end_date' => null,
            'project_manager' => 'mgr1',
            'priority' => 'Medium',
            'status' => 'Planning'
        ]);

        $result = $this->repository->find('1');

        $this->assertNull($result->getEndDate());
    }
}