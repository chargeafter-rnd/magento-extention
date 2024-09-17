<?php

namespace Chargeafter\Payment\Api;

use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * @api
 */
interface OrderTaxProcessInterface
{
    /**
     * Re-calculate tax for LTO
     *
     * @param OrderPaymentInterface $payment
     * @return void
     */
    public function reCalculate(OrderPaymentInterface $payment);
}
