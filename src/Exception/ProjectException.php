<?php
/**
 * Project Exception
 *
 * Base exception for project management operations
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

use Ksfraser\Exceptions\ProjectManagement\ProjectException as BaseProjectException;

/**
 * @BABOK Related: FR-PM-001 — base of the project-operations exception taxonomy
 */
class ProjectException extends BaseProjectException
{
    /**
     * @BABOK Related: FR-PM-001 — carries the failing project operation context
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}