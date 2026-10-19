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
    /** @var bool */
    private $propagationStopped = false;
    /** @var int */
    private $fileId;
    /** @var string */
    private $entityType;
    /** @var string */
    private $entityId;
    /** @var FileDTO */
    private $file;

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