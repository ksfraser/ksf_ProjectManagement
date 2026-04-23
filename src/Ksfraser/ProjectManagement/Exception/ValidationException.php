<?php
/**
 * Validation Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

use KsfExceptions\ValidationException as BaseValidationException;

class ValidationException extends ProjectException
{
    private array $errors;

    public function __construct(string $message, array $errors = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}