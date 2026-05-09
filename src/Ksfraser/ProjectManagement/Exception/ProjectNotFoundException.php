<?php
/**
 * Project Not Found Exception
 *
 * @package Ksfraser\ProjectManagement\Exception
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Exception;

use Ksfraser\Exceptions\ProjectManagement\ProjectNotFoundException as BaseProjectNotFoundException;

class ProjectNotFoundException extends BaseProjectNotFoundException
{
    public function __construct(string $projectId)
    {
        parent::__construct($projectId);
    }
}