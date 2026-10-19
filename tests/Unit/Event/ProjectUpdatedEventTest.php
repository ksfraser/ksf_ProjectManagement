<?php
/**
 * ProjectUpdatedEvent Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Event;

use DateTime;
use Ksfraser\ProjectManagement\Entity\Project;
use Ksfraser\ProjectManagement\Event\ProjectUpdatedEvent;
use PHPUnit\Framework\TestCase;

/**
 * @BABOK Related: FR-PM-001 — tests the project.updated event
 */
class ProjectUpdatedEventTest extends TestCase
{
    /** @var Project */
    private $project;

    /**
     * @BABOK Related: FR-PM-001 — project.updated event payload
     */
    protected function setUp(): void
    {
        $this->project = new Project('1', 'Test', 'Desc', new DateTime('2024-01-01'), 'mgr1');
    }

    /**
     * @BABOK Related: FR-PM-001 — project.updated event payload
     */
    public function testGetProject(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $this->assertSame($this->project, $event->getProject());
    }

    /**
     * @BABOK Related: FR-PM-001 — project.updated event payload
     */
    public function testGetChangedFields(): void
    {
        $changedFields = ['name', 'status'];
        $event = new ProjectUpdatedEvent($this->project, $changedFields);
        $this->assertSame($changedFields, $event->getChangedFields());
    }

    /**
     * @BABOK Related: FR-PM-001 — project.updated event payload
     */
    public function testGetChangedFieldsDefaultsToEmpty(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $this->assertSame([], $event->getChangedFields());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testStopPropagation(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}