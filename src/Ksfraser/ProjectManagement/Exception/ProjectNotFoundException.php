<?php
/**
 * Project Not Found Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

class ProjectNotFoundException extends ProjectException
{
    public function __construct(string $projectId)
    {
        parent::__construct("Project {$projectId} not found");
    }
}