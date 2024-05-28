<?php declare(strict_types=1);

namespace Ghost\Rabbit\Plugin;

use Psr\Log\LoggerInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\MessageQueue\Publisher;
use Exception;

class OrderManagement
{
    private const TOPIC_NAME = "queue.order.create.db";
    private const TOPIC_NAME_AMQP = "queue.order.create.rabbit";

    public function __construct(
        protected LoggerInterface $logger,
        protected Publisher $publisher
        ) {}

    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $return
    ) {
        try {
            $this->publisher->publish(self::TOPIC_NAME, $return->getId());
            $this->logger->info("Published message with OrderId = " . $return->getIncrementId());
        } catch (Exception $e) {
            $this->logger->error($e);
        }    
        return $return;
    }
}
