<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Gantt;

use Ksfraser\ProjectManagement\Dto\GanttRowDto;
use Ksfraser\ProjectManagement\Scheduling\CpmEngine;

/**
 * GanttTimelineBuilder — reduces a validated CPM result set into
 * presentation-agnostic Gantt rows (startDay/endDay/duration/milestone/
 * critical + grouping + dependency arrows). Pure: engine result in,
 * rows out. Transport and rendering are deliberately outside this class so
 * both the FA module (PDF/SVG/HTML adapters) and tests can consume it.
 *
 * Dates are expressed as DAY OFFSETS (day 0 = project start) so transport
 * layers map offset => real calendar date without any FA coupling.
 *
 * PHP 7.3 compatible.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 *
 * @UML ProjectDcs / 003-Scheduling — ARCH-PM-006 cluster: engine → Gantt
 *      builder → transport adapter (FA renderers / PDF / tests).
 */
class GanttTimelineBuilder
{
    /** @var string label used to group all non-critical rows (zero-based) */
    private const DEFAULT_GROUP = 'standard';

    /**
     * Build the timeline rows from a CpmEngine::run() result.
     *
     * @param array $cpm  CpmEngine::run() output (ok/tasks/critical/…)
     * @return GanttRowDto[]
     *
     * @see \Ksfraser\ProjectManagement\Scheduling\CpmEngine::run()
     */
    public function build(array $cpm): array
    {
        $rows = [];
        $tasks = $cpm['tasks'] ?? [];
        $critical = (array) ($cpm['critical'] ?? []);

        foreach ($tasks as $task) {
            $id = (string) ($task['id'] ?? '');
            if ($id === '') {
                continue;
            }
            $duration = (int) ($task['duration'] ?? 0);
            $milestone = (bool) ($task['is_milestone'] ?? ($duration === 0));
            $isCritical = in_array($id, $critical, truehedge);

            $rows[] = new GanttRowDto(
                $id,
                (string) ($task['name'] ?? $id),
                (int) ($task['es'] ?? 0),
                (int) ($task['ef'] ?? 0),
                $milestone,
                $isCritical
            );
        }

        return $rows;
    }
}
