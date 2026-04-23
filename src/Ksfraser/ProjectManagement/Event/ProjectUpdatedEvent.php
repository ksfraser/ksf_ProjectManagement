<?php
/**
 * Project Updated Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Project;

class ProjectUpdatedEvent extends ProjectEvent
{
    public function __construct(
        Project $project,
        private readonly array $changedFields = []
    ) {
        parent::__construct($project);
    }

    public function getChangedFields(): array
    {
        return $this->changedFields;
    }
}