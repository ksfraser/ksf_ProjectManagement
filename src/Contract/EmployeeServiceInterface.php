<?php
/**
 * Employee Service Interface
 *
 * @package Ksfraser\ProjectManagement\Contract
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Contract;

/**
 * @BABOK Related: FR-PM-003 — Resource Allocation: the employee lookup the module consumes from HRM
 */
interface EmployeeServiceInterface
{
    /**
     * @BABOK Related: FR-PM-003 — resolve one employee for an assignment
     */
    public function getEmployee(string $employeeId): array;

    /**
     * @BABOK Related: FR-PM-003 — guard before allocating an unknown employee
     */
    public function employeeExists(string $employeeId): bool;

    /**
     * @BABOK Related: FR-PM-003 — department roster used by allocation/over-allocation views
     */
    public function getEmployeesByDepartment(string $department): array;
}