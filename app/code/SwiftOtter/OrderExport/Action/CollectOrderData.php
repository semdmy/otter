<?php declare(strict_types=1);

namespace SwiftOtter\OrderExport\Action;

use Magento\Sales\Api\Data\OrderInterface;
use SwiftOtter\OrderExport\Api\OrderDataCollectorInterface;
use SwiftOtter\OrderExport\Model\HeaderData;

class CollectOrderData
{
    /**
     * @param OrderDataCollectorInterface[] $collectors
     */
    public function __construct(
        protected array $collectors = []
    ) {}

    /**
     * Execute
     *
     * @param OrderInterface $order
     * @param HeaderData $headerData
     * @return array
     */
    public function execute(OrderInterface $order, HeaderData $headerData): array
    {
        $output = [];
        foreach ($this->collectors as $collector) {
            $output = array_merge_recursive($output, $collector->collect($order, $headerData));
        }
        return $output;
    }
}
