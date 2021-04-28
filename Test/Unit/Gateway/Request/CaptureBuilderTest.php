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

use Chargeafter\Payment\Gateway\Request\CaptureBuilder;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class CaptureBuilderTest extends TestCase
{
    /**
     * @var CaptureBuilder
     */
    private $builder;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->builder = new CaptureBuilder();
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testBuild()
    {
        $expectedResult = [
            'storeId'=>1,
            'chargeId'=>123456,
            'payload'=>[
                'amount'=>10.35,
            ],
        ];
        $paymentMock = $this->createMock(Payment::class);
        $additionalInformation = [
            [
                'chargeId',
                123456
            ]
        ];
        $paymentMock->expects(self::exactly(count($additionalInformation)))
            ->method('getAdditionalInformation')
            ->willReturnMap($additionalInformation);

        $orderMock = $this->createMock(OrderAdapterInterface::class);
        $orderMock->expects(self::once())
            ->method('getStoreId')
            ->willReturn(1);

        $paymentDoMock = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDoMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($paymentMock);
        $paymentDoMock->expects(self::once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $buildSubject = [
            'payment' => $paymentDoMock,
            'amount' => 10.35
        ];
        self::assertEquals($expectedResult, $this->builder->build($buildSubject));
    }
}
