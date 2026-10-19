<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Entity;

/**
 * Link between an imported FA sales order and a project.
 *
 * @BABOK Related: FR-PM-009 - Sales order to project linkage
 * @since 1.0.0
 */
class ProjectSalesOrder
{
    /** @var int */
    private $id;
    /** @var string */
    private $projectId;
    /** @var int */
    private $faOrderNo;
    /** @var int */
    private $faTransType;
    /** @var string */
    private $source;
    /** @var string|null */
    private $sourceOrderId;
    /** @var string */
    private $linkedAt;

    public function __construct(array $data)
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->projectId = (string)$data['project_id'];
        $this->faOrderNo = (int)($data['fa_order_no'] ?? 0);
        $this->faTransType = (int)($data['fa_trans_type'] ?? 10);
        $this->source = (string)($data['source'] ?? 'all');
        $this->sourceOrderId = $data['source_order_id'] ?? null;
        $this->linkedAt = (string)($data['linked_at'] ?? '');
    }

    public function getId(): int { return $this->id; }
    public function getProjectId(): string { return $this->projectId; }
    public function getFaOrderNo(): int { return $this->faOrderNo; }
    public function getFaTransType(): int { return $this->faTransType; }
    public function getSource(): string { return $this->source; }
    public function getSourceOrderId(): ?string { return $this->sourceOrderId; }
    public function getLinkedAt(): string { return $this->linkedAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'fa_order_no' => $this->faOrderNo,
            'fa_trans_type' => $this->faTransType,
            'source' => $this->source,
            'source_order_id' => $this->sourceOrderId,
            'linked_at' => $this->linkedAt,
        ];
    }
}
