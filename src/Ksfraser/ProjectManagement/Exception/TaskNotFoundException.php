<?php
/**
 * Task Not Found Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

class TaskNotFoundException extends ProjectException
{
    public function __construct(string $taskId)
    {
        parent::__construct("Task {$taskId} not found");
    }
}