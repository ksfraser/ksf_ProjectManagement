<?php
/**
 * Project Not Found Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

use Ksfraser\Exceptions\ProjectManagement\ProjectNotFoundException as BaseProjectNotFoundException;

/**
 * @BABOK Related: FR-PM-001 — raised when a project lookup misses
 */
class ProjectNotFoundException extends BaseProjectNotFoundException
{
    /**
     * @BABOK Related: FR-PM-001 — names the project id that was not found
     */
    public function __construct(string $projectId)
    {
        parent::__construct($projectId);
    }
}