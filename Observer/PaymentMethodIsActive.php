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

namespace Chargeafter\Payment\Observer;

use Chargeafter\Payment\Helper\ApiHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class PaymentMethodIsActive
 * @package Chargeafter\Payment\Observer
 */
class PaymentMethodIsActive implements ObserverInterface
{
    /**
     * @var ApiHelper
     */
    protected $_apiHelper;

    /**
     * PaymentMethodIsActive constructor.
     * @param ApiHelper $apiHelper
     */
    public function __construct(ApiHelper $apiHelper)
    {
        $this->_apiHelper = $apiHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $methodInstance = $observer->getData('method_instance');

        if ($methodInstance->getCode() === 'chargeafter') {
            $quote = $observer->getData('quote');
            $storeId = $quote ? $quote->getStoreId() : null;
            if (!($this->_apiHelper->getPublicKey($storeId) && $this->_apiHelper->getPrivateKey($storeId))) {
                $result = $observer->getData('result');
                $result->setData('is_available', false);
            }
        }
    }
}
