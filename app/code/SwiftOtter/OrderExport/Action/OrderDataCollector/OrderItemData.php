<?php declare(strict_types=1);

namespace SwiftOtter\OrderExport\Action\OrderDataCollector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use SwiftOtter\OrderExport\Action\GetOrderExportItems;
use SwiftOtter\OrderExport\Api\OrderDataCollectorInterface;
use SwiftOtter\OrderExport\Model\HeaderData;

class OrderItemData implements OrderDataCollectorInterface
{
    /**
     * @param GetOrderExportItems $getOrderExportItems
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        protected GetOrderExportItems $getOrderExportItems,
        protected ProductRepositoryInterface $productRepository,
        protected SearchCriteriaBuilder $searchCriteriaBuilder
    ) {}

    /**
     * @param OrderInterface $order
     * @param HeaderData $headerData
     * @return array
     */
    #[\Override] public function collect(OrderInterface $order, HeaderData $headerData): array
    {
        $orderItems = $this->getOrderExportItems->execute($order);
        $skus = [];
        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            $skus[] = $orderItem->getSku();
        }
        $productsBySkus = $this->loadProducts($skus);
        $items = [];
        foreach ($orderItems as $orderItem) {
            $product = $productsBySkus[$orderItem->getSku()] ?? null;
            $items[] = $this->transform($orderItem, $product);
        }
        return [
            'items' => $items
        ];
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param ProductInterface $product
     * @return array
     */
    protected function transform(OrderItemInterface $orderItem, ?ProductInterface $product): array
    {
        return [
            'sku' => $this->getSku($orderItem, $product),
            'qty' => $orderItem->getQtyOrdered(),
            'item_price' => $orderItem->getBasePrice(),
            'item_cost' => $orderItem->getBaseCost(),
            'total' => $orderItem->getBaseRowTotal(),
        ];
    }

    /**
     * @param string[] $skus
     * @return ProductInterface[]
     */
    protected function loadProducts(array $skus): array
    {
        $this->searchCriteriaBuilder->addFilter('sku', $skus, 'in');
        /** @var ProductInterface[] $products */
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $productsBySkus = [];
        foreach ($products as $product) {
            $productsBySkus[$product->getSku()] = $product;
        }
        return $productsBySkus;
    }

    protected function getSku(OrderItemInterface $orderItem, ?ProductInterface $product): string
    {
        $sku = $orderItem->getSku();
        if (empty($product)) {
            return $sku;
        }
        $skuOverride = $product->getCustomAttribute('sku_override');
        $skuOverrideVal = ($skuOverride !== null) ? $skuOverride->getValue() : null;
        if (!empty($skuOverride)) {
            $sku = $skuOverrideVal;
        }
        return $sku;
    }
}
