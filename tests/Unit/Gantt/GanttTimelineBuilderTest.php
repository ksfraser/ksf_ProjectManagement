<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Gantt;

use Ksfraser\ProjectManagement\Scheduling\CpmEngine;
use Ksfraser\ProjectManagement\Gantt\GanttTimelineBuilder;
use PHPUnit\Framework\TestCase;

/**
 * GanttTimelineBuilderTest — engine result → presentation-agnostic Gantt rows.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
  *
 * @BABOK Related: FR-PM-006-001 — tests the CPM-to-Gantt projection
 */
final class GanttTimelineBuilderTest extends TestCase
{
    /**
     * @BABOK Related: FR-PM-006-001 — Gantt projection of the computed schedule
     */
    public function testBuildFromEngineResult(): void
    {
        $engine = new CpmEngine();
        $r = $engine->run(
            [['id' => 'a', 'name' => 'A', 'duration_days' => 8], ['id' => 'b', 'name' => 'B', 'duration_days' => 5]],
            [['predecessor_id' => 'a', 'task_id' => 'b', 'type' => 'fs', 'lag_days' => 0]]
        );
        $rows = (new GanttTimelineBuilder())->build($r);
        self::assertCount(2, $rows);
        $a = $rows[0];
        self::assertSame('a', $a->taskId);
        self::assertSame(0, $a->startDay);
        self::assertSame(8, $a->endDay);
        self::assertTrue($a->critical);
        $b = $rows[1];
        self::assertSame(8, $b->startDay);
        self::assertSame(13, $b->endDay);
    }
}
