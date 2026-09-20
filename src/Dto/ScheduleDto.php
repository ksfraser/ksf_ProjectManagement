<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Dto;

/**
 * ScheduleDto — result envelope returned by the scheduling service/engine.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 */
final class ScheduleDto
{
    /** @var bool */
    public $ok;
    /** @var string[] empty when ok */
    public $cycle;
    /** @var TaskDto[] keyed by task id */
    public $tasks;
    /** @var string[] critical task ids */
    public $critical;
    /** @var int */
    public $projectDuration;

    /**
     * @param TaskDto[] $tasks
     * @param string[]  $cycle
     * @param string[]  $critical
     */
    public function __construct(bool $ok, array $tasks, array $critical, int $projectDuration, array $cycle = [])
    {
        $this->ok = $ok;
        $this->tasks = $tasks;
        $this->critical = $critical;
        $this->projectDuration = $projectDuration;
        $this->cycle = $cycle;
    }
}
