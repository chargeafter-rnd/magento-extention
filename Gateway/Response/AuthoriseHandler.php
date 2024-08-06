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

namespace Chargeafter\Payment\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;

class AuthoriseHandler implements HandlerInterface
{
    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = $handlingSubject['payment']->getPayment();

        if (
            $response &&
            key_exists('offer', $response) &&
            key_exists('lender', $response['offer'])
        ) {
            $payment->setAdditionalInformation('lender', $response['offer']['lender']['name']);
        }

        $payment->setAdditionalInformation('chargeId', $response['id']);
        $payment->setAdditionalInformation('chargeTotalAmount', $response['totalAmount']);

        $data = $payment->getAdditionalInformation('data');
        $data = $data ? json_decode($data, true) : null;

        if (
            $data &&
            key_exists('lender', $data) &&
            key_exists('information', $data['lender'] ) &&
            key_exists('leaseId', $data['lender']['information'])
        ) {
            $payment->setAdditionalInformation('leaseId', $data['lender']['information']['leaseId']);
        }

        $payment->setTransactionId($response['id']);
        $payment->setIsTransactionClosed(false);
    }
}
