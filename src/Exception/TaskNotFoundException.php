<?php
/**
 * Task Not Found Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

use Ksfraser\Exceptions\ProjectManagement\TaskNotFoundException as BaseTaskNotFoundException;

/**
 * @BABOK Related: FR-PM-002 — raised when a task lookup misses
 */
class TaskNotFoundException extends BaseTaskNotFoundException
{
    /**
     * @BABOK Related: FR-PM-002 — names the task id that was not found
     */
    public function __construct(string $taskId)
    {
        parent::__construct($taskId);
    }
}