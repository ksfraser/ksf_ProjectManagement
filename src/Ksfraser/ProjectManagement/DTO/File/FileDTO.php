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

class FileDTO
{
    private ?int $id;
    private string $entityType;
    private string $entityId;
    private string $fileName;
    private string $originalName;
    private string $mimeType;
    private int $size;
    private string $storageType;
    private string $storagePath;
    private ?string $uploadedBy;
    private ?DateTime $uploadedAt;
    private string $description;
    private bool $inactive;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getStorageType(): string
    {
        return $this->storageType;
    }

    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    public function getUploadedBy(): ?string
    {
        return $this->uploadedBy;
    }

    public function getUploadedAt(): ?DateTime
    {
        return $this->uploadedAt;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isInactive(): bool
    {
        return $this->inactive;
    }

    public function getFullStoragePath(): string
    {
        return rtrim($this->storagePath, '/') . '/' . $this->fileName;
    }

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
            'uploaded_at' => $this->uploadedAt?->format(DateTimeInterface::ATOM),
            'description' => $this->description,
            'inactive' => $this->inactive,
        ];
    }

    public static function fromArray(array $data): self
    {
        $uploadedAt = null;
        if (!empty($data['uploaded_at'])) {
            $uploadedAt = $data['uploaded_at'] instanceof DateTime
                ? $data['uploaded_at']
                : new DateTime($data['uploaded_at']);
        }

        return new self(
            entityType: $data['entity_type'] ?? '',
            entityId: $data['entity_id'] ?? '',
            fileName: $data['file_name'] ?? '',
            originalName: $data['original_name'] ?? '',
            mimeType: $data['mime_type'] ?? 'application/octet-stream',
            size: (int) ($data['size'] ?? 0),
            storageType: $data['storage_type'] ?? 'local',
            storagePath: $data['storage_path'] ?? '',
            id: isset($data['id']) ? (int) $data['id'] : null,
            uploadedBy: $data['uploaded_by'] ?? null,
            uploadedAt: $uploadedAt,
            description: $data['description'] ?? '',
            inactive: (bool) ($data['inactive'] ?? false)
        );
    }

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
            entityType: $entityType,
            entityId: $entityId,
            fileName: $fileName,
            originalName: $originalName,
            mimeType: $mimeType,
            size: $size,
            uploadedBy: $uploadedBy,
            uploadedAt: new DateTime(),
            description: $description
        );
    }
}