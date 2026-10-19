<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Entity;

/**
 * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
 */
class ProjectType
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string|null */
    private $description;
    /** @var bool */
    private $inactive;
    /** @var int */
    private $sortOrder;

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function __construct(array $data)
    {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->inactive = (bool)($data['inactive'] ?? 0);
        $this->sortOrder = (int)($data['sort_order'] ?? 0);
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function getId(): int { return $this->id; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function getName(): string { return $this->name; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function getDescription(): ?string { return $this->description; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function isInactive(): bool { return $this->inactive; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function isActive(): bool { return !$this->inactive; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function getSortOrder(): int { return $this->sortOrder; }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for project-type reference data
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'inactive' => $this->inactive ? 1 : 0,
            'sort_order' => $this->sortOrder,
        ];
    }
}
