<?php
/**
 * Employee Service Interface
 *
 * @package Ksfraser\ProjectManagement\Contract
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Contract;

interface EmployeeServiceInterface
{
    public function getEmployee(string $employeeId): array;

    public function employeeExists(string $employeeId): bool;

    public function getEmployeesByDepartment(string $department): array;
}