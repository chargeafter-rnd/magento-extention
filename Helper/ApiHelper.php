<?php
/**
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

namespace Chargeafter\Payment\Helper;

use Chargeafter\Payment\Api\EnvironmentInterface;
use Magento\Payment\Gateway\ConfigInterface;

class ApiHelper
{
    const PRODUCTION_CDN_URL = "https://cdn.chargeafter.com";
    const SANDBOX_CDN_URL = "https://cdn-sandbox.ca-dev.co";
    const SANDBOX_API_URL = "https://api-sandbox.ca-dev.co";
    const PRODUCTION_API_URL = "https://api.chargeafter.com";
    const API_VERSION = "/v2";

    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * ApiHelper constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function isSandboxMode($storeId = null): bool
    {
        return $this->_config->getValue('environment', $storeId) === EnvironmentInterface::ENVIRONMENT_SANDBOX;
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getCdnUrl($storeId = null): string
    {
        return $this->isSandboxMode($storeId) ? self::SANDBOX_CDN_URL : self::PRODUCTION_CDN_URL;
    }

    /**
     * @param null $urn
     * @param null $storeId
     * @param false $withoutApiVersion
     * @return string
     */
    public function getApiUrl($urn = null, $storeId = null, bool $withoutApiVersion = false): string
    {
        $apiVersion = !$withoutApiVersion ? self::API_VERSION : '';
        return ( $this->isSandboxMode($storeId) ? self::SANDBOX_API_URL : self::PRODUCTION_API_URL )
            . $apiVersion . $urn;
    }

    /**
     * @param null $storeId
     * @return string|null
     */
    public function getPublicKey($storeId = null)
    {
        $value = $this->isSandboxMode($storeId)
            ? $this->_config->getValue('sandbox_public_key', $storeId)
            : $this->_config->getValue('production_public_key', $storeId);

        return $value ? trim($value) : null;
    }

    /**
     * @param null $storeId
     * @return string|null
     */
    public function getPrivateKey($storeId = null)
    {
        $value = $this->isSandboxMode($storeId)
            ? $this->_config->getValue('sandbox_private_key', $storeId)
            : $this->_config->getValue('production_private_key', $storeId);

        return $value ? trim($value) : null;
    }

    /**
     * @param $storeId
     * @return string|null
     */
    public function getTransactionType($storeId = null)
    {
        return $this->_config->getValue('transaction_type', $storeId);
    }
}
