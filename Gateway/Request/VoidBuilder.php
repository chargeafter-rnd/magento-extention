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

namespace Chargeafter\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class VoidBuilder
 * @package Chargeafter\Payment\Gateway\Request
 */
class VoidBuilder implements BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject): array
    {
        $payment = $buildSubject['payment']->getPayment();
        $order = $buildSubject['payment']->getOrder();
        return [
            'storeId'=>$order->getStoreId(),
            'chargeId'=>$payment->getAdditionalInformation('chargeId'),
        ];
    }
}
