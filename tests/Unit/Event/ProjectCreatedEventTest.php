<?php
/**
 * ProjectCreatedEvent Test
 *
 * @package Ksfraser\ProjectManagement\Tests\Unit\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Event;

use DateTime;
use Ksfraser\ProjectManagement\Entity\Project;
use Ksfraser\ProjectManagement\Event\ProjectCreatedEvent;
use PHPUnit\Framework\TestCase;

/**
 * @BABOK Related: FR-PM-001 — tests the project.created event
 */
class ProjectCreatedEventTest extends TestCase
{
    /** @var Project */
    private $project;

    /**
     * @BABOK Related: FR-PM-001 — project.created event payload
     */
    protected function setUp(): void
    {
        $this->project = new Project('1', 'Test', 'Desc', new DateTime('2024-01-01'), 'mgr1');
    }

    /**
     * @BABOK Related: FR-PM-001 — project.created event payload
     */
    public function testGetProject(): void
    {
        $event = new ProjectCreatedEvent($this->project);
        $this->assertSame($this->project, $event->getProject());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new ProjectCreatedEvent($this->project);
        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * @BABOK Related: PSR-14 plumbing under test; not itself a PM requirement
     */
    public function testStopPropagation(): void
    {
        $event = new ProjectCreatedEvent($this->project);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}