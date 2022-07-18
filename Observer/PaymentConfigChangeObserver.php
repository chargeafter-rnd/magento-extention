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

namespace Chargeafter\Payment\Observer;

use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Model\Brand\Brand;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class PaymentConfigChangeObserver implements ObserverInterface
{
    /**
     * @var ApiHelper
     */
    private $apiHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Brand
     */
    private $brand;

    /**
     * PaymentConfigChangeObserver constructor.
     *
     * @param ApiHelper $apiHelper
     * @param StoreManagerInterface $storeManager
     * @param Brand $brand
     */
    public function __construct(
        ApiHelper $apiHelper,
        StoreManagerInterface $storeManager,
        Brand $brand
    ) {
        $this->apiHelper = $apiHelper;
        $this->storeManager = $storeManager;
        $this->brand = $brand;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $changedPaths = (array) $observer->getEvent()->getChangedPaths();
        $chargeAfterPaths = array_reduce($changedPaths, [$this, '_matcher'], []);
        $storeId = $this->storeManager->getStore()->getId();

        if (!empty($chargeAfterPaths) && !empty($this->apiHelper->getPublicKey($storeId))) {
            $this->brand->configureLogo($storeId);
        }
    }

    /**
     * @param $m
     * @param $str
     * @return mixed
     */
    private function _matcher($m, $str)
    {
        if (preg_match('/payment\/chargeafter\/(\w+)/i', $str, $matches)) {
            if ($matches[1] != 'sort_order') {
                $m[] = $matches[1];
            }
        }

        return $m;
    }
}
