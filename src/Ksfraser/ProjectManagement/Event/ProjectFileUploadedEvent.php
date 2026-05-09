<?php
/**
 * Project File Uploaded Event
 *
 * @package Ksfraser\ProjectManagement\Event
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Event;

use Ksfraser\ProjectManagement\DTO\File\FileDTO;
use Psr\EventDispatcher\StoppableEventInterface;

class ProjectFileUploadedEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(
        private readonly int $fileId,
        private readonly string $entityType,
        private readonly string $entityId,
        private readonly FileDTO $file
    ) {
    }

    public function getFileId(): int
    {
        return $this->fileId;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getFile(): FileDTO
    {
        return $this->file;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}