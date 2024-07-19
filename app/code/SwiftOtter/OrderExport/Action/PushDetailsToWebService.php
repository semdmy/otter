<?php

namespace SwiftOtter\OrderExport\Action;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use SwiftOtter\OrderExport\Model\Config;
use  GuzzleHttp\Client;

class PushDetailsToWebService
{
    public function __construct(protected Config $config)
    {}

    /**
     * @throws GuzzleException
     */
    public function execute(array $exportDetails, OrderInterface $order): bool
    {
        $apiUrl = $this->config->getApiUrl(ScopeInterface::SCOPE_STORE, $order->getStoreId());
        $apiToken = $this->config->getApiToken(ScopeInterface::SCOPE_STORE, $order->getStoreId());

        $client = new Client();
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'body' => \json_encode($exportDetails)

        ];
        $client->post($apiUrl, $options);
        //TODO Make an HTTP request
        return true;
    }
}
