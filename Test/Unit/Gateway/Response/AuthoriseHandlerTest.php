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

use Chargeafter\Payment\Gateway\Response\AuthoriseHandler;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class AuthoriseHandlerTest
 * @package Chargeafter\Payment\Test\Unit\Gateway\Response
 */
class AuthoriseHandlerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHandle()
    {
        $response = [
            'id'=>'000001',
            'offer'=>[
                'lender'=>[
                    'name'=>'lender_name'
                ]
            ]
        ];
        $paymentMock = $this->createMock(Payment::class);
        $additionalInformation = [
            [$this->equalTo('lender'),$this->equalTo($response['offer']['lender']['name'])],
            [$this->equalTo('chargeId'),$this->equalTo($response['id'])]
        ];
        $paymentMock->expects($this->exactly(count($additionalInformation)))
            ->method('setAdditionalInformation')
            ->withConsecutive(...$additionalInformation);
        $paymentMock->expects($this->once())
            ->method('setTransactionId')
            ->with($response['id']);
        $paymentMock->expects($this->once())
            ->method('setIsTransactionClosed')
            ->with(false);
        $paymentDoMock = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDoMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($paymentMock);
        $handlingSubject = [
            'payment' => $paymentDoMock,
        ];
        $handler = new AuthoriseHandler();
        $handler->handle($handlingSubject, $response);
    }
}
