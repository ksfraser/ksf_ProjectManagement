<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Service;

use Ksfraser\ProjectManagement\Service\EventCloseTaskService;
use Ksfraser\ProjectManagement\Tests\Support\FakeDbConnection;
use PHPUnit\Framework\TestCase;

/**
 * EventCloseTaskService — FR-PM-007-002 task time-window close subscriber.
 *
 * Ported from ksf_FA_ProjectManagement/tests/Unit/EventCloseTaskServiceTest.
 * Same six behaviours, but driven through an injected FakeDbConnection
 * instead of the FA module's stubbed `db_*` globals — this package has no FA
 * runtime to stub. SQL is therefore asserted unprefixed (`fa_pm_task_progress`,
 * no `0_`) and values are asserted as BOUND PARAMETERS rather than as
 * interpolated literals, which is the stricter check.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 *
 * @BABOK Related: BR-007, FR-PM-007-002, FR-CAL-007-002
 */
final class EventCloseTaskServiceTest extends TestCase
{
    /** @var FakeDbConnection */
    private $db;

    /** @var EventCloseTaskService */
    private $service;

    protected function setUp(): void
    {
        $this->db = new FakeDbConnection();
        $this->service = new EventCloseTaskService($this->db);
    }

    /**
     * BON: window correctly equals closed_at - started_at recorded hours
     * (09:00 -> 13:00 = 4.0h), snapshotted onto the progress row (INSERT).
     */
    public function testTaskCloseSnapshotsWorkedWindow(): void
    {
        $this->db->queueSelects([
            [['task_id' => 'TSK-0001', 'project_id' => 'PRJ-0001']],
            [],
        ]);

        $result = $this->service->onEventClosed([
            'event_id' => 7,
            'task_id' => 'TSK-0001',
            'started_at' => '2026-09-14 09:00:00',
            'closed_at' => '2026-09-14 13:00:00',
        ]);

        $this->assertTrue($result['closed']);
        $this->assertSame(4.0, $result['recorded_hours']);
        $this->assertFalse($result['already_closed']);

        $writes = $this->db->writes();
        $this->assertCount(1, $writes);
        $this->assertStringContainsString('INSERT INTO fa_pm_task_progress', $writes[0]['sql']);
        $this->assertStringContainsString('work_based', $writes[0]['sql']);
        $this->assertSame('TSK-0001', $writes[0]['params']['task_id']);
        $this->assertSame(4.0, $writes[0]['params']['work_hours']);
        $this->assertSame(EventCloseTaskService::CLOSED_STATUS, $writes[0]['params']['status']);
    }

    /**
     * ARI: a repeat broadcast for the same, already-closed task is a NO-OP —
     * nothing re-written, window stays closed.
     */
    public function testAlreadyClosedTaskIsNoOp(): void
    {
        $this->db->queueSelects([
            [['task_id' => 'TSK-0001', 'project_id' => 'PRJ-0001']],
            [['progress_id' => 1, 'task_id' => 'TSK-0001', 'status' => 'Closed']],
        ]);

        $result = $this->service->onEventClosed([
            'task_id' => 'TSK-0001',
            'started_at' => '2026-09-14 09:00:00',
            'closed_at' => '2026-09-14 13:00:00',
        ]);

        $this->assertTrue($result['closed']);
        $this->assertTrue($result['already_closed']);
        $this->assertNoWriteStatements($this->db->writes());
    }

    /**
     * An existing non-closed progress row is marked closed with the window.
     */
    public function testExistingTaskWindowIsSealed(): void
    {
        $this->db->queueSelects([
            [['task_id' => 'TSK-0001', 'project_id' => 'PRJ-0001']],
            [['progress_id' => 1, 'task_id' => 'TSK-0001', 'status' => 'In Progress']],
        ]);

        $result = $this->service->onEventClosed([
            'task_id' => 'TSK-0001',
            'started_at' => '2026-09-14 09:00:00',
            'closed_at' => '2026-09-14 11:30:00',
        ]);

        $this->assertTrue($result['closed']);
        $this->assertSame(2.5, $result['recorded_hours']);
        $this->assertFalse($result['already_closed']);

        $writes = $this->db->writes();
        $this->assertCount(1, $writes);
        $this->assertStringContainsString('UPDATE fa_pm_task_progress', $writes[0]['sql']);
        $this->assertSame(2.5, $writes[0]['params']['work_hours']);
        $this->assertSame(EventCloseTaskService::CLOSED_STATUS, $writes[0]['params']['status']);
        $this->assertSame('TSK-0001', $writes[0]['params']['task_id']);
    }

    /**
     * AZZ: DTO task_id unknown to this module -> no-op, no error.
     */
    public function testUnknownTaskNoOp(): void
    {
        $this->db->queueSelects([[]]);

        $result = $this->service->onEventClosed(['task_id' => 'TSK-9999']);

        $this->assertFalse($result['closed']);
        $this->assertSame('unknown_task', $result['reason']);
        $this->assertNoWriteStatements($this->db->writes());
    }

    /**
     * No task_id -> no-op (subscriber must not guess the task).
     */
    public function testNoTaskIdNoOp(): void
    {
        $result = $this->service->onEventClosed(['event_id' => 7]);

        $this->assertFalse($result['closed']);
        $this->assertSame('no_task_id', $result['reason']);
        $this->assertSame([], $this->db->writes());
    }

    /**
     * Object-form DTO (toArray) is accepted without a Calendar class dependency.
     */
    public function testObjectDtoIsAccepted(): void
    {
        $this->db->queueSelects([
            [['task_id' => 'TSK-0001', 'project_id' => 'PRJ-0001']],
            [],
        ]);

        $dto = new class([]) {
            /** @var array<string,mixed> */
            public $payload;

            /**
             * @param array<string,mixed> $payload
             */
            public function __construct(array $payload)
            {
                $this->payload = $payload;
            }

            /**
             * @return array<string,mixed>
             */
            public function toArray(): array
            {
                return $this->payload;
            }
        };
        $dto->payload = [
            'task_id' => 'TSK-0001',
            'started_at' => '2026-09-14 09:00:00',
            'closed_at' => '2026-09-14 09:30:00',
        ];

        $result = $this->service->onEventClosed($dto);

        $this->assertTrue($result['closed']);
        $this->assertSame(0.5, $result['recorded_hours']);
        $this->assertStringContainsString('INSERT INTO fa_pm_task_progress', $this->db->lastSql());
    }

    /**
     * @param array<int,array{sql:string,params:array}> $writes
     * @return void
     */
    private function assertNoWriteStatements(array $writes): void
    {
        foreach ($writes as $write) {
            $prefix = strtolower(substr(ltrim((string) $write['sql']), 0, 6));
            $this->assertNotContains($prefix, ['insert', 'update', 'delete', 'replac']);
        }
    }
}