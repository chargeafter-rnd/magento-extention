<?php
/**
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      eduard.samoilenko@chargeafter.com
 */

namespace Chargeafter\Payment\Model\Brand;

use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Model\Brand\Deferred\BrandDeferred;
use Chargeafter\Payment\Model\Ui\ConfigProvider;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Brand
 * @package Chargeafter\Payment\Model\Brand
 */
class Brand
{
    private static $defaultLogo = 'chargeafter';

    /**
     * @var Config
     */
    private $resourceConfig;

    /**
     * @var BrandDeferred
     */
    private $brandDeferred;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ApiHelper
     */
    private $apiHelper;

    /**
     * Brand constructor.
     *
     * @param Config $resourceConfig
     * @param BrandDeferred $brandDeferred
     * @param ScopeConfigInterface $scopeConfig
     * @param ApiHelper $apiHelper
     */
    public function __construct(
        Config $resourceConfig,
        BrandDeferred $brandDeferred,
        ScopeConfigInterface $scopeConfig,
        ApiHelper $apiHelper
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->brandDeferred = $brandDeferred;
        $this->scopeConfig = $scopeConfig;
        $this->apiHelper = $apiHelper;
    }

    /**
     * @param $storeId
     */
    public function configureLogo($storeId)
    {
        $merchantId = $this->scopeConfig->getValue(
            $this->generateConfigPath(
                $this->getMerchantConfigKey($storeId)
            )
        );

        $brand = $this->brandDeferred->get($merchantId);
        $brandId = $brand['brandId'] ?? self::$defaultLogo;

        if (isset($brand['merchantId'])) {
            $this->resourceConfig->saveConfig(
                $this->generateConfigPath(
                    $this->getMerchantConfigKey($storeId)
                ),
                $brand['merchantId']
            );
        }

        $this->resourceConfig->saveConfig($this->generateConfigPath('logo'), $brandId);
    }

    /**
     * @param string $slug
     * @return string
     */
    private function generateConfigPath(string $slug): string
    {
        return 'payment/' . ConfigProvider::CODE . '/' . $slug;
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getMerchantConfigKey($storeId): string
    {
        return 'merchant_' . mb_substr(trim($this->apiHelper->getPublicKey($storeId)), 0, 20);
    }
}
