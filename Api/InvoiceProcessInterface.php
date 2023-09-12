<?php

namespace Chargeafter\Payment\Api;

use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * @api
 */
interface InvoiceProcessInterface
{
    /**
     * Invoice order
     *
     * @param OrderPaymentInterface $payment
     *
     * @return void
     */
    public function invoice(OrderPaymentInterface $payment);
}
