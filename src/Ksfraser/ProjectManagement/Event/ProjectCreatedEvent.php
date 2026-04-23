<?php
/**
 * Project Created Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Project;

class ProjectCreatedEvent extends ProjectEvent
{
    public function __construct(Project $project)
    {
        parent::__construct($project);
    }
}