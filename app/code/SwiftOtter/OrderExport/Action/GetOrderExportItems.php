<?php declare(strict_types=1);

namespace SwiftOtter\OrderExport\Action;

use Magento\Sales\Api\Data\OrderInterface;

class GetOrderExportItems
{
    /**
     * @param array $allowedTypes
     */
    public function __construct(protected array $allowedTypes = [])
    {
    }

    /**
     * Execute
     *
     * @param OrderInterface $order
     * @return array
     */
    public function execute(OrderInterface $order): array
    {
        $items = [];
        foreach ($order->getItems() as $item) {
            if (in_array($item->getProductType(), $this->allowedTypes)) {
                $items[] = $item;
            }
        }
        return $items;
    }
}
