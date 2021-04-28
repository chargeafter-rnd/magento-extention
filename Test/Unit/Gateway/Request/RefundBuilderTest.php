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

namespace Chargeafter\Payment\Test\Unit\Gateway\Request;

use Chargeafter\Payment\Gateway\Request\RefundBuilder;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class RefundBuilderTest
 * @package Chargeafter\Payment\Test\Unit\Gateway\Request
 */
class RefundBuilderTest extends TestCase
{
    /**
     * @return void
     * @throws ReflectionException
     */
    public function testBuild()
    {
        $expectedResult = [
            'storeId'=>1,
            'chargeId'=>1234567,
            'payload'=>[
                'amount'=>34.54,
            ],
        ];
        $paymentMock = $this->createMock(Payment::class);
        $additionalInfo = [
            [
                'chargeId',
                1234567,
            ]
        ];
        $paymentMock->expects(self::exactly(count($additionalInfo)))
            ->method('getAdditionalInformation')
            ->willReturnMap($additionalInfo);
        $orderMock = $this->createMock(OrderAdapterInterface::class);
        $orderMock->expects(self::once())
            ->method('getStoreId')
            ->willReturn(1);

        $paymentDOMock = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDOMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($paymentMock);
        $paymentDOMock->expects(self::once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $builder = new RefundBuilder();
        $buildSubject = [
            'payment' => $paymentDOMock,
            'amount' => 34.54,
        ];
        self::assertEquals($expectedResult, $builder->build($buildSubject));
    }
}
