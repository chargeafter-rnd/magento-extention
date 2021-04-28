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

use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class ApiHelper
 * @package Chargeafter\Payment\Helper
 */
class ApiHelper
{
    const PRODUCTION_CDN_URL = "https://cdn.chargeafter.com";
    const SANDBOX_CDN_URL = "https://cdn-sandbox.ca-dev.co";
    const SANDBOX_API_URL = "https://api-sandbox.ca-dev.co/v1";
    const PRODUCTION_API_URL = "https://api.chargeafter.com/v1";
    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * ApiHelper constructor.
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->_config = $config;
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getCdnUrl($storeId = null)
    {
        return $this->_config->getValue('environment', $storeId)==='sandbox' ? self::SANDBOX_CDN_URL : self::PRODUCTION_CDN_URL;
    }

    /**
     * @param null $urn
     * @param null $storeId
     * @return string
     */
    public function getApiUrl($urn=null, $storeId = null)
    {
        return ($this->_config->getValue('environment', $storeId)==='sandbox' ? self::SANDBOX_API_URL : self::PRODUCTION_API_URL) . $urn;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPublicKey($storeId = null)
    {
        return $this->_config->getValue('environment', $storeId)==='sandbox' ? $this->_config->getValue('sandbox_public_key', $storeId) : $this->_config->getValue('production_public_key', $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPrivateKey($storeId = null)
    {
        return $this->_config->getValue('environment', $storeId)==='sandbox' ? $this->_config->getValue('sandbox_private_key', $storeId) : $this->_config->getValue('production_private_key', $storeId);
    }
}
