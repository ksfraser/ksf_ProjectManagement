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
    private int $fileId;
    private string $entityType;
    private string $entityId;
    private FileDTO $file;

    public function __construct(
        int $fileId,
        string $entityType,
        string $entityId,
        FileDTO $file
    ) {
        $this->fileId = $fileId;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->file = $file;
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