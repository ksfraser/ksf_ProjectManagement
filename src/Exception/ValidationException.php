<?php
/**
 * Validation Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

use Ksfraser\Exceptions\ProjectManagement\ProjectValidationException as BaseProjectValidationException;

/**
 * @BABOK Related: N/A — generic validation error raised by service input guards; no single requirement owns it
 */
class ValidationException extends BaseProjectValidationException
{
}