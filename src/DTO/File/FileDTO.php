<?php
/**
 * FileDTO - Data Transfer Object for project/task attachments
 *
 * @package Ksfraser\ProjectManagement\DTO\File
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\DTO\File;

use DateTime;
use DateTimeInterface;

/**
 * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
 */
class FileDTO
{
    /** @var int|null */
    private $id;
    /** @var string */
    private $entityType;
    /** @var string */
    private $entityId;
    /** @var string */
    private $fileName;
    /** @var string */
    private $originalName;
    /** @var string */
    private $mimeType;
    /** @var int */
    private $size;
    /** @var string */
    private $storageType;
    /** @var string */
    private $storagePath;
    /** @var string|null */
    private $uploadedBy;
    /** @var DateTime|null */
    private $uploadedAt;
    /** @var string */
    private $description;
    /** @var bool */
    private $inactive;

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function __construct(
        string $entityType,
        string $entityId,
        string $fileName,
        string $originalName,
        string $mimeType,
        int $size,
        string $storageType = 'local',
        string $storagePath = '',
        ?int $id = null,
        ?string $uploadedBy = null,
        ?DateTime $uploadedAt = null,
        string $description = '',
        bool $inactive = false
    ) {
        $this->id = $id;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->fileName = $fileName;
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->storageType = $storageType;
        $this->storagePath = $storagePath;
        $this->uploadedBy = $uploadedBy;
        $this->uploadedAt = $uploadedAt;
        $this->description = $description;
        $this->inactive = $inactive;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getId(): ?int
    {
        return $this->id;
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
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getStorageType(): string
    {
        return $this->storageType;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getUploadedBy(): ?string
    {
        return $this->uploadedBy;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getUploadedAt(): ?DateTime
    {
        return $this->uploadedAt;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function isInactive(): bool
    {
        return $this->inactive;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getFullStoragePath(): string
    {
        return rtrim($this->storagePath, '/') . '/' . $this->fileName;
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $size = $this->size;

        while ($size >= 1024 && $i < 4) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function isImage(): bool
    {
        return in_array($this->mimeType, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ], true);
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function isDocument(): bool
    {
        return in_array($this->mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ], true);
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'file_name' => $this->fileName,
            'original_name' => $this->originalName,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'storage_type' => $this->storageType,
            'storage_path' => $this->storagePath,
            'uploaded_by' => $this->uploadedBy,
            'uploaded_at' => $this->uploadedAt !== null ? $this->uploadedAt->format(DateTimeInterface::ATOM) : null,
            'description' => $this->description,
            'inactive' => $this->inactive,
        ];
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public static function fromArray(array $data): self
    {
        $uploadedAt = null;
        if (!empty($data['uploaded_at'])) {
            $uploadedAt = $data['uploaded_at'] instanceof DateTime
                ? $data['uploaded_at']
                : new DateTime($data['uploaded_at']);
        }

        return new self(
            $data['entity_type'] ?? '',
            $data['entity_id'] ?? '',
            $data['file_name'] ?? '',
            $data['original_name'] ?? '',
            $data['mime_type'] ?? 'application/octet-stream',
            (int) ($data['size'] ?? 0),
            $data['storage_type'] ?? 'local',
            $data['storage_path'] ?? '',
            isset($data['id']) ? (int) $data['id'] : null,
            $data['uploaded_by'] ?? null,
            $uploadedAt,
            $data['description'] ?? '',
            (bool) ($data['inactive'] ?? false)
        );
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public static function fromUploadedFile(
        string $entityType,
        string $entityId,
        string $tmpName,
        string $originalName,
        ?string $uploadedBy = null,
        string $description = ''
    ): self {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        $size = filesize($tmpName);
        $fileName = uniqid('proj_', true) . '_' . basename($originalName);

        return new self(
            $entityType,
            $entityId,
            $fileName,
            $originalName,
            $mimeType,
            $size,
            'local',
            '',
            null,
            $uploadedBy,
            new DateTime(),
            $description
        );
    }
}