<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Dto;

/**
 * TaskDto — immutable input row for the CPM scheduler.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 */
final class TaskDto
{
    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var int duration in days (0 = milestone) */
    public $duration;
    /** @var bool */
    public $isMilestone;
    /** @var bool */
    public $isCritical;
    /** @var float */
    public $slack;
    /** @var int */
    public $es;
    /** @var int */
    public $ef;
    /** @var int */
    public $ls;
    /** @var int */
    public $lf;
    /** @var array<int,array<string,mixed>> */
    public $constraints;

    public function __construct(string $id, string $name, int $duration, bool $isMilestone, array $constraints = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->duration = $duration;
        $this->isMilestone = $isMilestone;
        $this->constraints = $constraints;
        $this->isCritical = false;
        $this->slack = 0.0;
        $this->es = 0;
        $this->ef = $duration;
        $this->ls = 0;
        $this->lf = 0;
    }
}
