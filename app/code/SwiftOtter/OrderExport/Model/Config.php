<?php declare(strict_types=1);

namespace SwiftOtter\OrderExport\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const CONFIG_PATH_ENABLED = 'sales/order_export/enabled';
    public const CONFIG_PATH_API_URL = 'sales/order_export/api_url';
    public const CONFIG_PATH_API_TOKEN = 'sales/order_export/api_tocken';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig
    )
    {
    }

    /**
     * Is order export enabled
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function isEnabled(string $scopeType = ScopeInterface::SCOPE_STORE, ?string $scopeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED, $scopeType, $scopeCode);
    }

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return string
     */
    public function getApiUrl(string $scopeType = ScopeInterface::SCOPE_STORE, ?string $scopeCode = null): string
    {
        $value =  $this->scopeConfig->getValue(self::CONFIG_PATH_API_URL, $scopeType, $scopeCode);
        return ($value !== null) ? (string) $value : '';
    }

    /**
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return string
     */
    public function getApiToken(string $scopeType = ScopeInterface::SCOPE_STORE, ?string $scopeCode = null): string
    {
        $value =  $this->scopeConfig->getValue(self::CONFIG_PATH_API_TOKEN, $scopeType, $scopeCode);
        return ($value !== null) ? (string) $value : '';
    }
}
