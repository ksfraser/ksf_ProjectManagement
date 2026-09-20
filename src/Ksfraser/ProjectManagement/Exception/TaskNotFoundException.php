<?php
/**
 * Task Not Found Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

use Ksfraser\Exceptions\ProjectManagement\TaskNotFoundException as BaseTaskNotFoundException;

class TaskNotFoundException extends BaseTaskNotFoundException
{
    public function __construct(string $taskId)
    {
        parent::__construct($taskId);
    }
}