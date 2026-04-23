<?php
/**
 * Project File Service
 *
 * Handles file attachments for projects and tasks
 * Delegates to ksfraser/file package for storage
 *
 * @package Ksfraser\ProjectManagement
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement;

use Ksfraser\ProjectManagement\Contract\DatabaseAdapterInterface;
use Ksfraser\ProjectManagement\Contract\FileServiceInterface;
use Ksfraser\ProjectManagement\DTO\File\FileDTO;
use Ksfraser\ProjectManagement\Event\ProjectFileUploadedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class ProjectFileService
{
    private const TABLE_FILES = 'fa_pm_files';
    private const TABLE_PREFIX = 'fa_pm_';

    public function __construct(
        private readonly DatabaseAdapterInterface $db,
        private readonly FileServiceInterface $fileService,
        private readonly EventDispatcherInterface $events,
        private readonly LoggerInterface $logger
    ) {
    }

    public function attachFile(FileDTO $file, string $uploadedTmpPath): bool
    {
        $this->logger->info('Attaching file', [
            'entity_type' => $file->getEntityType(),
            'entity_id' => $file->getEntityId(),
            'original_name' => $file->getOriginalName()
        ]);

        $storageDir = $this->getStorageDir($file->getEntityType());

        if (!$this->fileService->upload($file, $storageDir)) {
            $this->logger->error('Failed to upload file', ['file' => $file->getFileName()]);
            return false;
        }

        $fileId = $this->saveFileRecord($file, $storageDir);

        $this->events->dispatch(new ProjectFileUploadedEvent(
            (int) $fileId,
            $file->getEntityType(),
            $file->getEntityId(),
            $file
        ));

        $this->logger->info('File attached successfully', ['file_id' => $fileId]);
        return true;
    }

    public function getFilesForEntity(string $entityType, string $entityId): array
    {
        $sql = "SELECT * FROM " . self::TABLE_PREFIX . "files
                WHERE entity_type = ? AND entity_id = ? AND inactive = 0
                ORDER BY uploaded_at DESC";

        $rows = $this->db->fetchAll($sql, [$entityType, $entityId]);

        return array_map(fn($row) => FileDTO::fromArray($row), $rows);
    }

    public function getFile(int $fileId): ?FileDTO
    {
        $sql = "SELECT * FROM " . self::TABLE_PREFIX . "files WHERE id = ?";
        $row = $this->db->fetchAssoc($sql, [(string) $fileId]);

        if (!$row) {
            return null;
        }

        return FileDTO::fromArray($row);
    }

    public function deleteFile(int $fileId): bool
    {
        $file = $this->getFile($fileId);

        if (!$file) {
            return false;
        }

        if (!$this->fileService->delete($file->getFileName(), $file->getStoragePath())) {
            $this->logger->warning('Could not delete physical file', [
                'file' => $file->getFileName(),
                'path' => $file->getStoragePath()
            ]);
        }

        $sql = "UPDATE " . self::TABLE_PREFIX . "files SET inactive = 1 WHERE id = ?";
        $this->db->executeUpdate($sql, [(string) $fileId]);

        $this->logger->info('File deleted', ['file_id' => $fileId]);
        return true;
    }

    public function downloadFile(int $fileId): ?string
    {
        $file = $this->getFile($fileId);

        if (!$file) {
            return null;
        }

        return $this->fileService->download($file->getFileName(), $file->getStoragePath());
    }

    public function getFileUrl(int $fileId): ?string
    {
        $file = $this->getFile($fileId);

        if (!$file) {
            return null;
        }

        return $this->fileService->getUrl($file->getFileName(), $file->getStoragePath());
    }

    private function getStorageDir(string $entityType): string
    {
        return match ($entityType) {
            'project' => 'projects',
            'task' => 'tasks',
            default => 'misc',
        };
    }

    private function saveFileRecord(FileDTO $file, string $storagePath): int|string
    {
        $sql = "INSERT INTO " . self::TABLE_PREFIX . "files (
                    entity_type, entity_id, file_name, original_name,
                    mime_type, size, storage_type, storage_path,
                    uploaded_by, uploaded_at, description
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

        $this->db->executeUpdate($sql, [
            $file->getEntityType(),
            $file->getEntityId(),
            $file->getFileName(),
            $file->getOriginalName(),
            $file->getMimeType(),
            $file->getSize(),
            $file->getStorageType(),
            $storagePath,
            $file->getUploadedBy(),
            $file->getDescription()
        ]);

        return $this->db->lastInsertId();
    }
}