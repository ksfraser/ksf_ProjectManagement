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

use KsfExceptions\KsfException;
use Throwable;

class ProjectException extends KsfException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}