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

/**
 * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
 */
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

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
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

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getFileId(): int
    {
        return $this->fileId;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getFile(): FileDTO
    {
        return $this->file;
    }

    /**
     * @BABOK Related: PSR-14 plumbing; carries no PM requirement
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * @BABOK Related: PSR-14 plumbing; carries no PM requirement
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}