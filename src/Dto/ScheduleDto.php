<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Dto;

/**
 * ScheduleDto — result envelope returned by the scheduling service/engine.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
  *
 * @BABOK Related: FR-PM-006-001 — result envelope of the schedule computation
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
      *
 * @BABOK Related: FR-PM-006-001 — carries ES/EF/LS/LF/slack/critical per task, the ok flag and the cycle path
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
