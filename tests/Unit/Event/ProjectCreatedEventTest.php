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

class ProjectCreatedEventTest extends TestCase
{
    private Project $project;

    protected function setUp(): void
    {
        $this->project = new Project('1', 'Test', 'Desc', new DateTime('2024-01-01'), 'mgr1');
    }

    public function testGetProject(): void
    {
        $event = new ProjectCreatedEvent($this->project);
        $this->assertSame($this->project, $event->getProject());
    }

    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new ProjectCreatedEvent($this->project);
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testStopPropagation(): void
    {
        $event = new ProjectCreatedEvent($this->project);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}