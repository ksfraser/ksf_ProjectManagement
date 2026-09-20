<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Dto;

/**
 * DependencyDto — immutable precedence edge for the CPM scheduler.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 */
final class DependencyDto
{
    /** @var string */
    public $predecessor;
    /** @var string */
    public $successor;
    /** @var string one of fs|ss|ff|sf */
    public $type;
    /** @var int */
    public $lag;

    public function __construct(string $predecessor, string $successor, string $type = 'fs', int $lag = 0)
    {
        $this->predecessor = $predecessor;
        $this->successor = $successor;
        $this->type = $type;
        $this->lag = $lag;
    }
}
