<?php
/**
 * File Service Interface
 *
 * @package Ksfraser\ProjectManagement\Contract
 */

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Contract;

use Ksfraser\ProjectManagement\DTO\File\FileDTO;

/**
 * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
 */
interface FileServiceInterface
{
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function upload(FileDTO $file, string $destinationDir): bool;

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function download(string $fileName, string $storagePath): ?string;

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function delete(string $fileName, string $storagePath): bool;

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function getUrl(string $fileName, string $storagePath): string;

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it
     */
    public function exists(string $fileName, string $storagePath): bool;
}