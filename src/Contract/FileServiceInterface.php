<?php
/**
 * File Service Interface
 *
 * @package Ksfraser\ProjectManagement\Contract
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Contract;

use Ksfraser\ProjectManagement\DTO\File\FileDTO;

interface FileServiceInterface
{
    public function upload(FileDTO $file, string $destinationDir): bool;

    public function download(string $fileName, string $storagePath): ?string;

    public function delete(string $fileName, string $storagePath): bool;

    public function getUrl(string $fileName, string $storagePath): string;

    public function exists(string $fileName, string $storagePath): bool;
}