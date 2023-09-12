<?php

namespace Chargeafter\Payment\Api;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;

/**
 * @api
 */
interface OrderTaxProcessInterface
{
    /**
     * Re-calculate tax for LTO
     *
     * @param OrderPaymentInterface $payment
     * @return mixed
     */
    public function reCalculate(OrderPaymentInterface $payment);
}
