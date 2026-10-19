<?php
/**
 * Project Updated Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\Entity\Project;

/**
 * @BABOK Related: FR-PM-001 — the project.updated integration event
 */
class ProjectUpdatedEvent extends ProjectEvent
{
    /** @var array */
    private $changedFields = [];

    /**
     * @BABOK Related: FR-PM-001 — emits project.updated and names the changed fields
     */
    public function __construct(
        Project $project,
        array $changedFields = []
    ) {
        $this->changedFields = $changedFields;
        parent::__construct($project);
    }

    /**
     * @BABOK Related: FR-PM-001 — emits project.updated and names the changed fields
     */
    public function getChangedFields(): array
    {
        return $this->changedFields;
    }
}