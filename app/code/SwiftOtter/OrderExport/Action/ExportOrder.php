<?php declare(strict_types=1);

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use SwiftOtter\OrderExport\Model\HeaderData;
use SwiftOtter\OrderExport\Model\Config;
use SwiftOtter\OrderExport\Action\PushDetailsToWebService;

class ExportOrder
{
    public function __construct(
        protected Config $config,
        protected OrderRepositoryInterface $orderRepository,
        protected CollectOrderData $collectOrderData,
        protected PushDetailsToWebService $pushDetailsToWebService,
        protected SaveExportDetailsToOrder $saveExportDetailsToOrder
    ) {}

    /**
     * @param int $orderId
     * @param HeaderData $headerData
     * @return array
     * @throws LocalizedException
     * @throws GuzzleException
     */
    public function execute(int $orderId, HeaderData $headerData): array
    {
        $order = $this->orderRepository->get($orderId);
        if (!$this->config->isEnabled(ScopeInterface::SCOPE_STORE, $order->getStoreId())) {
            throw new LocalizedException(__('Order export is disabled.'));
        }
        $results = ['success' => true, 'error' => null];
        $exportData =  $this->collectOrderData->execute($order, $headerData);
        //$results['success'] = $this->pushDetailsToWebService->execute($exportData, $order);
        $this->saveExportDetailsToOrder->execute($order, $headerData, $results);
        //TODO Save export details
        return $results;
    }
}
