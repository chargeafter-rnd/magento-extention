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

namespace Chargeafter\Payment\Model\Brand\Deferred;

use Chargeafter\Payment\Service\SessionService;
use Magento\Framework\Async\DeferredInterface;

/**
 * Class Deferred
 * @package Chargeafter\Payment\Deferred
 */
class BrandDeferred implements DeferredInterface
{
    private const DEFAULT_BRAND = "chargeafter";

    /**
     * @var bool
     */
    private $done = false;

    /**
     * @var array
     */
    private $brand = [];

    /**
     * @var string
     */
    private $merchantId = "";

    /**
     * @var SessionService
     */
    private $sessionService;

    /**
     * BrandDeferred constructor.
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * @param null $merchantId
     * @return array
     */
    public function get($merchantId = null): array
    {
        if (!$this->brand) {
            $this->brand = [
                'brandId'    => $this->getBrandIdFromSettings($merchantId, self::DEFAULT_BRAND),
                'merchantId' => $this->getMerchantId()
            ];
            $this->done = true;
        }

        return $this->brand;
    }

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * @param $merchantId
     * @param string|null $default
     * @return string|null
     */
    private function getBrandIdFromSettings($merchantId, $default = null):? string
    {
        if (empty($merchantId)) {
            $sessionId = $this->sessionService->createSession();
            if (empty($sessionId)) {
                return $default;
            }

            $merchantId = $this->sessionService->getMerchantBySession($sessionId);
            if (empty($merchantId)) {
                return $default;
            }
        }

        $this->merchantId = $merchantId;

        $settings = $this->sessionService->getSettingByMerchant($merchantId);
        if (empty($settings)) {
            return $default;
        }

        return key_exists('brandId', $settings)
                ? mb_strtolower($settings['brandId'])
                : $default;
    }

    /**
     * @return string
     */
    private function getMerchantId(): string
    {
        return $this->merchantId;
    }
}
