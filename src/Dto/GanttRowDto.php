<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Dto;

/**
 * GanttRowDto — one rendered Gantt bar (presentation-agnostic primitive).
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
  *
 * @BABOK Related: FR-PM-006-001 — one Gantt bar derived from a CPM result
 */
final class GanttRowDto
{
    /** @var string */
    public $taskId;
    /** @var string */
    public $label;
    /** @var int */
    public $startDay;
    /** @var int */
    public $endDay;
    /** @var bool */
    public $milestone;
    /** @var bool */
    public $critical;

    /**
     * @BABOK Related: FR-PM-006-001 — presentation-agnostic projection of a scheduled task
     */
    public function __construct(string $taskId, string $label, int $startDay, int $endDay, bool $milestone, bool $critical)
    {
        $this->taskId = $taskId;
        $this->label = $label;
        $this->startDay = $startDay;
        $this->endDay = $endDay;
        $this->milestone = $milestone;
        $this->critical = $critical;
    }
}
