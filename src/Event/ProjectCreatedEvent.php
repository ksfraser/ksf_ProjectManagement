<?php
/**
 * Project Created Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Project;

/**
 * @BABOK Related: FR-PM-001 — the project.created integration event
 */
class ProjectCreatedEvent extends ProjectEvent
{
    /**
     * @BABOK Related: FR-PM-001 — emits project.created on a new project
     */
    public function __construct(Project $project)
    {
        parent::__construct($project);
    }
}