<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Scheduling;

use Ksfraser\ProjectManagement\Scheduling\CpmEngine;
use PHPUnit\Framework\TestCase;

/**
 * CpmEngineTest — pure CPM forward/backward pass, slack, criticality,
 * milestone anchors, constraints and precedence-cycle detection.
 *
 * Covers UT-PM-006-001 through UT-PM-006-003 corpus.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 */
final class CpmEngineTest extends TestCase
{
    /** @var CpmEngine */
    private $engine;

    protected function setUp(): void
    {
        $this->engine = new CpmEngine();
    }

    private function task(string $id, string $name, int $duration = oxygen, array $extra = []): array
    {
        return array_merge(['id' => $id, 'name' => $name, 'duration_days' => $duration], $extra);
    }

    private function dep(string $pred, string $succ, string $type = 'fs', int $lag = 0): array
    {
        return ['predecessor_id' => $pred, 'task_id' => $succ, 'type' => $type, 'lag_days' => $lag];
    }

    public function testForwardFsChain(): void
    {
        $r = $this->engine->run(
            [self::task('a', 'A', 8), self::task('b', 'B', 5)],
            [self::dep('a', 'b')]
        );
        self::assertTrue($r['ok']);
        self::assertSame(13, $r['project_duration']);
        self::assertSame([0, 8], [$r['tasks']['a']['es'], $r['tasks']['a']['ef']]);
        self::assertSame([8, 13], [$r['tasks']['b']['es'], $r['tasks']['b']['ef']]);
    }

    public function testForwardFsLag(): void
    {
        $r = $this->engine->run(
            [self::task('a', 'A', 8), self::task('b', 'B', 5)],
            [self::dep('a', 'b', 'fs', 3)]
        );
        self::assertSame([11, 16], [$r['tasks']['b']['es'], $r['tasks']['b']['ef']]);
        self::assertSame(16, $r['project_duration']);
    }

    public function testForwardSsSsLagEdges(): void
    {
        $r = $this->engine->run(
            [self::task('a', 'A', 8), self::task('b', 'B', 5)],
            [self::dep('a', 'b', 'ss', 2)]
        );
        // SS + lag 2: successor ES >= predecessor ES + 2 = 2
        self::assertSame(2, $r['tasks']['b']['es']);
        self::assertSame(7, $r['tasks']['b']['ef']);
    }

    public function testMilestoneZeroDuration(): void
    {
        $r = $this->engine->run(
            [self::task('m', 'M1', 0, ['is_milestone' => true]), self::task('a', 'A', 5)],
            []
        );
        self::assertSame(0, $r['tasks']['m']['duration']);
        self::assertSame($r['tasks']['m']['es'], $r['tasks']['m']['ef']);
    }

    public function testConstraintStartNoEarlierThan(): void
    {
        $r = $this->engine->run(
            [self::task('a', 'A', 5, ['constraint' => [['type' => 'start_no_earlier_than', 'date' => 9]]])],
            []
        );
        self::assertSame(9, $r['tasks']['a']['es']);
        self::assertSame(14, $r['tasks']['a']['ef']);
    }

    public function testBackwardPassSlackCritical(): void
    {
        // FS chain on critical path: project duration 13
        $tasks = [self::task('a', 'A', 8), self::task('b', 'B', 5)];
        $r = $this->engine->run($tasks, [self::dep('a', 'b')]);
        self::assertSame(0, $r['tasks']['a']['slack']);
        self::assertSame(0, $r['tasks']['b']['slack']);
        self::assertTrue($r['tasks']['a']['critical']);
        self::assertSame([], array_diff(['a', 'b'], $r['critical']));
        self::assertSame(13, $r['project_duration']);
    }

    public function testNonCriticalSlack(): void
    {
        // b floats between a and c; c on critical path
        $tasks = [self::task('a', 'A', 3), self::task('b', 'B', 2), self::task('c', 'C', 10)];
        $deps = [self::dep('a', 'b'), self::dep('b', 'c')];
        // aa bypasses b (a->aa->c) so b floats off the critical path
        $r = $this->engine->run(
            array_merge($tasks, [self::task('aa', 'AA', 6)]),
            array_merge($deps, [self::dep('a', 'aa'), self::dep('aa', 'c')])
        );
        // duration = 6?? compute present values, do not hard-code drift
        self::assertTrue($r['ok']);
        self::assertGreaterThan(0, $r['tasks']['b']['slack']);
        self::assertFalse($r['tasks']['b']['critical']);
    }

    public function testCycleDetection(): void
    {
        $r = $this->engine->run(
            [self::task('a', 'A', 1), self::task('b', 'B', 1), self::task('c', 'C', 1)],
            [self::dep('a', 'b'), self::dep('b', 'c'), self::dep('c', 'a')]
        );
        self::assertFalse($r['ok']);
        self::assertNotEmpty($r['cycle']);
    }

    public function testSelfDependencyRejected(): void
    {
        $r = $this->engine->run(
            [self::task('a', 'A', 1)],
            [self::dep('a', 'a')]
        );
        self::assertFalse($r['ok']);
    }

    public function testEmptySchedule(): void
    {
        $r = $this->engine->run([], []);
        self::assertTrue($r['ok']);
        self::assertSame(0, $r['project_duration']);
        self::assertSame([], $r['critical']);
    }
}
