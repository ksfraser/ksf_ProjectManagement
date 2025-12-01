<?php
/**
 * FrontAccounting Project Management Exceptions
 *
 * Custom exceptions for project management functionality.
 *
 * @package FA\Modules\ProjectManagement
 * @version 1.0.0
 * @author FrontAccounting Team
 * @license GPL-3.0
 */

namespace FA\Modules\ProjectManagement;

/**
 * Project Exception
 *
 * Base exception for project management operations
 */
class ProjectException extends \Exception
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}