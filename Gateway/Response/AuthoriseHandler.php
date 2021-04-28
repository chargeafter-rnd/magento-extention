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

/**
 * Class AuthoriseHandler
 * @package Chargeafter\Payment\Gateway\Response
 */
class AuthoriseHandler implements HandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = $handlingSubject['payment']->getPayment();
        $payment->setAdditionalInformation('lender', $response['offer']['lender']['name']);
        $payment->setAdditionalInformation('chargeId', $response['id']);
        $payment->setTransactionId($response['id']);
        $payment->setIsTransactionClosed(false);
    }
}
