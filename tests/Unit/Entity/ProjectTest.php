<?php
/**
 * Project Entity Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Entity
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Entity;

use DateTime;
use Ksfraser\ProjectManagement\Entity\Project;
use PHPUnit\Framework\TestCase;

/**
 * @BABOK Related: FR-PM-001, FR-PM-004, FR-PM-005 — tests the Project entity
 */
class ProjectTest extends TestCase
{
    /** @var Project */
    private $project;
    /** @var DateTime */
    private $startDate;

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    protected function setUp(): void
    {
        $this->startDate = new DateTime('2024-01-01');
        $this->project = new Project('1', 'Test Project', 'Test Description', $this->startDate, 'manager1');
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testConstructorSetsRequiredFields(): void
    {
        $this->assertSame('1', $this->project->getProjectId());
        $this->assertSame('Test Project', $this->project->getName());
        $this->assertSame('Test Description', $this->project->getDescription());
        $this->assertSame($this->startDate, $this->project->getStartDate());
        $this->assertSame('manager1', $this->project->getProjectManager());
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testConstructorSetsDefaultValues(): void
    {
        $this->assertNull($this->project->getEndDate());
        $this->assertSame(0.0, $this->project->getBudget());
        $this->assertSame('', $this->project->getCustomerId());
        $this->assertSame('Medium', $this->project->getPriority());
        $this->assertSame('Planning', $this->project->getStatus());
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testSetNameReturnsSelf(): void
    {
        $result = $this->project->setName('New Name');
        $this->assertSame($this->project, $result);
        $this->assertSame('New Name', $this->project->getName());
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testSetDescriptionReturnsSelf(): void
    {
        $result = $this->project->setDescription('New Description');
        $this->assertSame($this->project, $result);
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testSetStartDateReturnsSelf(): void
    {
        $newDate = new DateTime('2024-02-01');
        $result = $this->project->setStartDate($newDate);
        $this->assertSame($this->project, $result);
        $this->assertSame($newDate, $this->project->getStartDate());
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testSetEndDateReturnsSelf(): void
    {
        $endDate = new DateTime('2024-12-31');
        $result = $this->project->setEndDate($endDate);
        $this->assertSame($this->project, $result);
        $this->assertSame($endDate, $this->project->getEndDate());
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testSetEndDateCanBeNull(): void
    {
        $this->project->setEndDate(new DateTime('2024-12-31'));
        $this->project->setEndDate(null);
        $this->assertNull($this->project->getEndDate());
    }

    /**
     * @BABOK Related: FR-PM-001 — set budget (UC-PM-005)
     */
    public function testSetBudgetReturnsSelf(): void
    {
        $result = $this->project->setBudget(50000.00);
        $this->assertSame($this->project, $result);
        $this->assertSame(50000.00, $this->project->getBudget());
    }

    /**
     * @BABOK Related: FR-PM-001 — link to CRM customer (BR-PM-003)
     */
    public function testSetCustomerIdReturnsSelf(): void
    {
        $result = $this->project->setCustomerId('CUST001');
        $this->assertSame($this->project, $result);
        $this->assertSame('CUST001', $this->project->getCustomerId());
    }

    /**
     * @BABOK Related: FR-PM-001 — assign project manager (BR-PM-004)
     */
    public function testSetProjectManagerReturnsSelf(): void
    {
        $result = $this->project->setProjectManager('newManager');
        $this->assertSame($this->project, $result);
        $this->assertSame('newManager', $this->project->getProjectManager());
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testSetPriorityReturnsSelf(): void
    {
        $result = $this->project->setPriority('High');
        $this->assertSame($this->project, $result);
        $this->assertSame('High', $this->project->getPriority());
    }

    /**
     * @BABOK Related: FR-PM-001 — status lifecycle (BR-PM-005)
     */
    public function testSetStatusReturnsSelf(): void
    {
        $result = $this->project->setStatus('Active');
        $this->assertSame($this->project, $result);
        $this->assertSame('Active', $this->project->getStatus());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — duration calculation
     */
    public function testGetDurationReturnsNullWhenNoEndDate(): void
    {
        $this->assertNull($this->project->getDuration());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.1 — duration calculation
     */
    public function testGetDurationReturnsDaysDifference(): void
    {
        $endDate = new DateTime('2024-01-11');
        $this->project->setEndDate($endDate);
        $this->assertSame(10, $this->project->getDuration());
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function testIsOverdueReturnsFalseWhenNoEndDate(): void
    {
        $this->assertFalse($this->project->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-005 — a completed task is never overdue
     */
    public function testIsOverdueReturnsFalseWhenCompleted(): void
    {
        $pastDate = new DateTime('2020-01-01');
        $this->project->setEndDate($pastDate);
        $this->project->setStatus('Completed');
        $this->assertFalse($this->project->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function testIsOverdueReturnsTrueWhenPastEndDateAndNotCompleted(): void
    {
        $pastDate = new DateTime('2020-01-01');
        $this->project->setEndDate($pastDate);
        $this->project->setStatus('Active');
        $this->assertTrue($this->project->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-005 — overdue detection
     */
    public function testIsOverdueReturnsFalseWhenFutureEndDate(): void
    {
        $futureDate = new DateTime('2030-01-01');
        $this->project->setEndDate($futureDate);
        $this->assertFalse($this->project->isOverdue());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — active status
     */
    public function testIsActiveReturnsTrueForActiveProject(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setEndDate(new DateTime('+1 month'));
        $this->project->setStatus('Active');
        $this->assertTrue($this->project->isActive());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — active status
     */
    public function testIsActiveReturnsFalseForCompletedProject(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setEndDate(new DateTime('+1 month'));
        $this->project->setStatus('Completed');
        $this->assertFalse($this->project->isActive());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — active status
     */
    public function testIsActiveReturnsFalseForCancelledProject(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setEndDate(new DateTime('+1 month'));
        $this->project->setStatus('Cancelled');
        $this->assertFalse($this->project->isActive());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — active status
     */
    public function testIsActiveReturnsFalseForFutureProject(): void
    {
        $this->project->setStartDate(new DateTime('+1 month'));
        $this->project->setEndDate(new DateTime('+2 months'));
        $this->assertFalse($this->project->isActive());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — active status
     */
    public function testIsActiveReturnsFalseForPastProject(): void
    {
        $this->project->setStartDate(new DateTime('-2 months'));
        $this->project->setEndDate(new DateTime('-1 month'));
        $this->assertFalse($this->project->isActive());
    }

    /**
     * @BABOK Related: FR-PM-004 §4.3 — active status
     */
    public function testIsActiveReturnsTrueForProjectWithNoEndDate(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setStatus('Active');
        $this->assertTrue($this->project->isActive());
    }

    /**
     * @BABOK Related: FR-PM-001 — project entity behaviour
     */
    public function testToArrayReturnsAllFields(): void
    {
        $endDate = new DateTime('2024-12-31');
        $this->project->setEndDate($endDate);
        $this->project->setBudget(100000.00);
        $this->project->setCustomerId('CUST001');
        $this->project->setPriority('High');
        $this->project->setStatus('Active');

        $array = $this->project->toArray();

        $this->assertSame('1', $array['project_id']);
        $this->assertSame('Test Project', $array['name']);
        $this->assertSame('Test Description', $array['description']);
        $this->assertNotEmpty($array['start_date']);
        $this->assertNotEmpty($array['end_date']);
        $this->assertSame(100000.00, $array['budget']);
        $this->assertSame('CUST001', $array['customer_id']);
        $this->assertSame('manager1', $array['project_manager']);
        $this->assertSame('High', $array['priority']);
        $this->assertSame('Active', $array['status']);
    }
}