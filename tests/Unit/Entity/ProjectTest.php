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

class ProjectTest extends TestCase
{
    private Project $project;
    private DateTime $startDate;

    protected function setUp(): void
    {
        $this->startDate = new DateTime('2024-01-01');
        $this->project = new Project('1', 'Test Project', 'Test Description', $this->startDate, 'manager1');
    }

    public function testConstructorSetsRequiredFields(): void
    {
        $this->assertSame('1', $this->project->getProjectId());
        $this->assertSame('Test Project', $this->project->getName());
        $this->assertSame('Test Description', $this->project->getDescription());
        $this->assertSame($this->startDate, $this->project->getStartDate());
        $this->assertSame('manager1', $this->project->getProjectManager());
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $this->assertNull($this->project->getEndDate());
        $this->assertSame(0.0, $this->project->getBudget());
        $this->assertSame('', $this->project->getCustomerId());
        $this->assertSame('Medium', $this->project->getPriority());
        $this->assertSame('Planning', $this->project->getStatus());
    }

    public function testSetNameReturnsSelf(): void
    {
        $result = $this->project->setName('New Name');
        $this->assertSame($this->project, $result);
        $this->assertSame('New Name', $this->project->getName());
    }

    public function testSetDescriptionReturnsSelf(): void
    {
        $result = $this->project->setDescription('New Description');
        $this->assertSame($this->project, $result);
    }

    public function testSetStartDateReturnsSelf(): void
    {
        $newDate = new DateTime('2024-02-01');
        $result = $this->project->setStartDate($newDate);
        $this->assertSame($this->project, $result);
        $this->assertSame($newDate, $this->project->getStartDate());
    }

    public function testSetEndDateReturnsSelf(): void
    {
        $endDate = new DateTime('2024-12-31');
        $result = $this->project->setEndDate($endDate);
        $this->assertSame($this->project, $result);
        $this->assertSame($endDate, $this->project->getEndDate());
    }

    public function testSetEndDateCanBeNull(): void
    {
        $this->project->setEndDate(new DateTime('2024-12-31'));
        $this->project->setEndDate(null);
        $this->assertNull($this->project->getEndDate());
    }

    public function testSetBudgetReturnsSelf(): void
    {
        $result = $this->project->setBudget(50000.00);
        $this->assertSame($this->project, $result);
        $this->assertSame(50000.00, $this->project->getBudget());
    }

    public function testSetCustomerIdReturnsSelf(): void
    {
        $result = $this->project->setCustomerId('CUST001');
        $this->assertSame($this->project, $result);
        $this->assertSame('CUST001', $this->project->getCustomerId());
    }

    public function testSetProjectManagerReturnsSelf(): void
    {
        $result = $this->project->setProjectManager('newManager');
        $this->assertSame($this->project, $result);
        $this->assertSame('newManager', $this->project->getProjectManager());
    }

    public function testSetPriorityReturnsSelf(): void
    {
        $result = $this->project->setPriority('High');
        $this->assertSame($this->project, $result);
        $this->assertSame('High', $this->project->getPriority());
    }

    public function testSetStatusReturnsSelf(): void
    {
        $result = $this->project->setStatus('Active');
        $this->assertSame($this->project, $result);
        $this->assertSame('Active', $this->project->getStatus());
    }

    public function testGetDurationReturnsNullWhenNoEndDate(): void
    {
        $this->assertNull($this->project->getDuration());
    }

    public function testGetDurationReturnsDaysDifference(): void
    {
        $endDate = new DateTime('2024-01-11');
        $this->project->setEndDate($endDate);
        $this->assertSame(10, $this->project->getDuration());
    }

    public function testIsOverdueReturnsFalseWhenNoEndDate(): void
    {
        $this->assertFalse($this->project->isOverdue());
    }

    public function testIsOverdueReturnsFalseWhenCompleted(): void
    {
        $pastDate = new DateTime('2020-01-01');
        $this->project->setEndDate($pastDate);
        $this->project->setStatus('Completed');
        $this->assertFalse($this->project->isOverdue());
    }

    public function testIsOverdueReturnsTrueWhenPastEndDateAndNotCompleted(): void
    {
        $pastDate = new DateTime('2020-01-01');
        $this->project->setEndDate($pastDate);
        $this->project->setStatus('Active');
        $this->assertTrue($this->project->isOverdue());
    }

    public function testIsOverdueReturnsFalseWhenFutureEndDate(): void
    {
        $futureDate = new DateTime('2030-01-01');
        $this->project->setEndDate($futureDate);
        $this->assertFalse($this->project->isOverdue());
    }

    public function testIsActiveReturnsTrueForActiveProject(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setEndDate(new DateTime('+1 month'));
        $this->project->setStatus('Active');
        $this->assertTrue($this->project->isActive());
    }

    public function testIsActiveReturnsFalseForCompletedProject(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setEndDate(new DateTime('+1 month'));
        $this->project->setStatus('Completed');
        $this->assertFalse($this->project->isActive());
    }

    public function testIsActiveReturnsFalseForCancelledProject(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setEndDate(new DateTime('+1 month'));
        $this->project->setStatus('Cancelled');
        $this->assertFalse($this->project->isActive());
    }

    public function testIsActiveReturnsFalseForFutureProject(): void
    {
        $this->project->setStartDate(new DateTime('+1 month'));
        $this->project->setEndDate(new DateTime('+2 months'));
        $this->assertFalse($this->project->isActive());
    }

    public function testIsActiveReturnsFalseForPastProject(): void
    {
        $this->project->setStartDate(new DateTime('-2 months'));
        $this->project->setEndDate(new DateTime('-1 month'));
        $this->assertFalse($this->project->isActive());
    }

    public function testIsActiveReturnsTrueForProjectWithNoEndDate(): void
    {
        $this->project->setStartDate(new DateTime('-1 month'));
        $this->project->setStatus('Active');
        $this->assertTrue($this->project->isActive());
    }

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