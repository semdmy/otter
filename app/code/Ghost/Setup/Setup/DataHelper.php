<?php

declare(strict_types=1);

namespace Ghost\Setup\Setup;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Store\Model\ScopeInterface;

class DataHelper
{
    public function __construct(protected ConfigInterface $config)
    {
    }

    public function initStoreLocale(int $storeId, string $locale): void
    {
        $this->config->saveConfig(
            'general/locale/code',
            $locale,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }
}
