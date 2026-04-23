<?php
/**
 * ProjectAssignment Entity Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Entity
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Entity;

use DateTime;
use Ksfraser\ProjectManagement\Entity\ProjectAssignment;
use PHPUnit\Framework\TestCase;

class ProjectAssignmentTest extends TestCase
{
    private ProjectAssignment $assignment;
    private DateTime $startDate;

    protected function setUp(): void
    {
        $this->startDate = new DateTime('2024-01-01');
        $this->assignment = new ProjectAssignment('proj1', 'emp1', 'Developer', $this->startDate, 100.0);
    }

    public function testConstructorSetsRequiredFields(): void
    {
        $this->assertSame('proj1', $this->assignment->getProjectId());
        $this->assertSame('emp1', $this->assignment->getEmployeeId());
        $this->assertSame('Developer', $this->assignment->getRole());
        $this->assertSame($this->startDate, $this->assignment->getStartDate());
        $this->assertSame(100.0, $this->assignment->getAllocationPercentage());
    }

    public function testConstructorSetsEndDateToNull(): void
    {
        $this->assertNull($this->assignment->getEndDate());
    }

    public function testSetRoleReturnsSelf(): void
    {
        $result = $this->assignment->setRole('Manager');
        $this->assertSame($this->assignment, $result);
        $this->assertSame('Manager', $this->assignment->getRole());
    }

    public function testSetStartDateReturnsSelf(): void
    {
        $newDate = new DateTime('2024-02-01');
        $result = $this->assignment->setStartDate($newDate);
        $this->assertSame($this->assignment, $result);
        $this->assertSame($newDate, $this->assignment->getStartDate());
    }

    public function testSetEndDateReturnsSelf(): void
    {
        $endDate = new DateTime('2024-12-31');
        $result = $this->assignment->setEndDate($endDate);
        $this->assertSame($this->assignment, $result);
        $this->assertSame($endDate, $this->assignment->getEndDate());
    }

    public function testSetEndDateCanBeNull(): void
    {
        $this->assignment->setEndDate(new DateTime('2024-12-31'));
        $this->assignment->setEndDate(null);
        $this->assertNull($this->assignment->getEndDate());
    }

    public function testSetAllocationPercentageClampsToValidRange(): void
    {
        $this->assignment->setAllocationPercentage(-10.0);
        $this->assertSame(0.0, $this->assignment->getAllocationPercentage());

        $this->assignment->setAllocationPercentage(150.0);
        $this->assertSame(100.0, $this->assignment->getAllocationPercentage());

        $this->assignment->setAllocationPercentage(75.0);
        $this->assertSame(75.0, $this->assignment->getAllocationPercentage());
    }

    public function testSetAllocationPercentageReturnsSelf(): void
    {
        $result = $this->assignment->setAllocationPercentage(50.0);
        $this->assertSame($this->assignment, $result);
    }

    public function testIsActiveReturnsTrueWhenWithinDateRange(): void
    {
        $this->assignment->setStartDate(new DateTime('-1 month'));
        $this->assignment->setEndDate(new DateTime('+1 month'));
        $this->assertTrue($this->assignment->isActive());
    }

    public function testIsActiveReturnsTrueWhenNoEndDate(): void
    {
        $this->assignment->setStartDate(new DateTime('-1 month'));
        $this->assertTrue($this->assignment->isActive());
    }

    public function testIsActiveReturnsFalseWhenStartDateInFuture(): void
    {
        $this->assignment->setStartDate(new DateTime('+1 month'));
        $this->assertFalse($this->assignment->isActive());
    }

    public function testIsActiveReturnsFalseWhenEndDateInPast(): void
    {
        $this->assignment->setStartDate(new DateTime('-2 months'));
        $this->assignment->setEndDate(new DateTime('-1 month'));
        $this->assertFalse($this->assignment->isActive());
    }

    public function testIsEndedReturnsFalseWhenNoEndDate(): void
    {
        $this->assertFalse($this->assignment->isEnded());
    }

    public function testIsEndedReturnsFalseWhenEndDateInFuture(): void
    {
        $this->assignment->setEndDate(new DateTime('+1 month'));
        $this->assertFalse($this->assignment->isEnded());
    }

    public function testIsEndedReturnsTrueWhenEndDateInPast(): void
    {
        $this->assignment->setEndDate(new DateTime('-1 month'));
        $this->assertTrue($this->assignment->isEnded());
    }

    public function testToArrayReturnsAllFields(): void
    {
        $endDate = new DateTime('2024-12-31');
        $this->assignment->setEndDate($endDate);
        $this->assignment->setRole('Lead Developer');
        $this->assignment->setAllocationPercentage(75.0);

        $array = $this->assignment->toArray();

        $this->assertSame('proj1', $array['project_id']);
        $this->assertSame('emp1', $array['employee_id']);
        $this->assertSame('Lead Developer', $array['role']);
        $this->assertNotEmpty($array['start_date']);
        $this->assertNotEmpty($array['end_date']);
        $this->assertSame(75.0, $array['allocation_percentage']);
    }
}