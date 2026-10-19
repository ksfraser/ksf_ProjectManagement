<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Entity;

/**
 * Recognized project revenue from an imported FA order.
 *
 * @BABOK Related: N/A — no requirement defined in this repo for revenue
 *   recognition. Replaces the previous `@BABOK FR-PM-010`, which matches no
 *   ProjectDcs document in this repo (FA-side FR-PM-010 is the app-shell/tab
 *   surface, a different subject).
 * @since 1.0.0
 */
class ProjectRevenue
{
    /** @var int */
    private $revenueId;
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
    /** @var float */
    private $orderTotal;
    /** @var float */
    private $revenueAmount;
    /** @var string */
    private $orderDate;
    /** @var string */
    private $createdAt;

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function __construct(array $data)
    {
        $this->revenueId = (int)($data['revenue_id'] ?? 0);
        $this->projectId = (string)$data['project_id'];
        $this->faOrderNo = (int)($data['fa_order_no'] ?? 0);
        $this->faTransType = (int)($data['fa_trans_type'] ?? 10);
        $this->source = (string)($data['source'] ?? 'all');
        $this->sourceOrderId = $data['source_order_id'] ?? null;
        $this->orderTotal = (float)($data['order_total'] ?? 0);
        $this->revenueAmount = (float)($data['revenue_amount'] ?? 0);
        $this->orderDate = (string)($data['order_date'] ?? '');
        $this->createdAt = (string)($data['created_at'] ?? '');
    }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getRevenueId(): int { return $this->revenueId; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getProjectId(): string { return $this->projectId; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getFaOrderNo(): int { return $this->faOrderNo; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getFaTransType(): int { return $this->faTransType; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getSource(): string { return $this->source; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getSourceOrderId(): ?string { return $this->sourceOrderId; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getOrderTotal(): float { return $this->orderTotal; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getRevenueAmount(): float { return $this->revenueAmount; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getOrderDate(): string { return $this->orderDate; }
    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function getCreatedAt(): string { return $this->createdAt; }

    /**
     * @BABOK Related: N/A — no requirement defined in this repo for revenue recognition; see the class docblock
     */
    public function toArray(): array
    {
        return [
            'revenue_id' => $this->revenueId,
            'project_id' => $this->projectId,
            'fa_order_no' => $this->faOrderNo,
            'fa_trans_type' => $this->faTransType,
            'source' => $this->source,
            'source_order_id' => $this->sourceOrderId,
            'order_total' => $this->orderTotal,
            'revenue_amount' => $this->revenueAmount,
            'order_date' => $this->orderDate,
            'created_at' => $this->createdAt,
        ];
    }
}
