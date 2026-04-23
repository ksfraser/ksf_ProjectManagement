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

class ProjectUpdatedEventTest extends TestCase
{
    private Project $project;

    protected function setUp(): void
    {
        $this->project = new Project('1', 'Test', 'Desc', new DateTime('2024-01-01'), 'mgr1');
    }

    public function testGetProject(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $this->assertSame($this->project, $event->getProject());
    }

    public function testGetChangedFields(): void
    {
        $changedFields = ['name', 'status'];
        $event = new ProjectUpdatedEvent($this->project, $changedFields);
        $this->assertSame($changedFields, $event->getChangedFields());
    }

    public function testGetChangedFieldsDefaultsToEmpty(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $this->assertSame([], $event->getChangedFields());
    }

    public function testIsPropagationNotStoppedByDefault(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testStopPropagation(): void
    {
        $event = new ProjectUpdatedEvent($this->project);
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}