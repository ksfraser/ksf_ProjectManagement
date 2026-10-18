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
    private array $changedFields = [];

    public function __construct(
        Project $project,
        array $changedFields = []
    ) {
        $this->changedFields = $changedFields;
        parent::__construct($project);
    }

    public function getChangedFields(): array
    {
        return $this->changedFields;
    }
}