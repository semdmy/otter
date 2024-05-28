<?php declare(strict_types=1);

namespace Ghost\Rabbit\Model;

use Psr\Log\LoggerInterface;
use Exception;

class Consumer
{
    public function __construct(protected LoggerInterface $logger) {}

    public function processOrder(string $orderId)
    {
        try {
            $this->logger->info("Consumes the order with id " . $orderId);
        } catch (Exception $e) {
            $this->logger->error($e);
            return;
        }
    }
}