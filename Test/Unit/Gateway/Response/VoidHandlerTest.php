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

namespace Chargeafter\Payment\Test\Unit\Gateway\Response;

use Chargeafter\Payment\Gateway\Response\VoidHandler;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class VoidHandlerTest
 * @package Chargeafter\Payment\Test\Unit\Gateway\Response
 */
class VoidHandlerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHandle()
    {
        $response = [
            'result' => [
                'id'=> '1234567'
            ],
        ];

        $paymentMock = $this->createMock(Payment::class);
        $paymentMock->expects($this->once())
            ->method('setTransactionId')
            ->with($this->equalTo($response['result']['id']));
        $paymentDoMock = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDoMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentMock);
        $handlingSubject = [
            'payment' => $paymentDoMock,
        ];
        $handler = new VoidHandler();

        $handler->handle($handlingSubject, $response);
    }
}
