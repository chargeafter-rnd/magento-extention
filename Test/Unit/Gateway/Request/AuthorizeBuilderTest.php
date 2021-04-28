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
use Chargeafter\Payment\Gateway\Request\AuthorizeBuilder;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use ReflectionException;

class AuthorizeBuilderTest extends TestCase
{
    /**
     * @var AuthorizeBuilder
     */
    private $builder;

    /**
     * @var PaymentDataObjectInterface|MockObject
     */
    private $paymentDOMock;
    /**
     * @var Payment|MockObject
     */
    private $paymentMock;
    /**
     * @var OrderAdapterInterface|MockObject
     */
    private $orderMock;

    /**
     * @inheritdoc
     * @throws ReflectionException
     */
    protected function setUp()
    {
        $this->builder = new AuthorizeBuilder();
        $this->paymentDOMock = $this->createMock(PaymentDataObjectInterface::class);
        $this->paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderMock = $this->getMockBuilder(OrderAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

    }
    /**
     * @return void
     */
    public function testBuild()
    {
        $additionalData = [
            [
                'token',
                '123456',
            ],
        ];
        $expectedResult = [
            'storeId'  => 0,
            'payload' => [
                'confirmationToken'=>'123456',
                'merchantOrderId'=>'000000101',
            ]
        ];
        $buildSubject = [
            'payment' => $this->paymentDOMock,
        ];
        $this->orderMock->expects(self::once())
            ->method('getOrderIncrementId')
            ->willReturn('000000101');
        $this->paymentMock->expects(self::exactly(count($additionalData)))
            ->method('getAdditionalInformation')
            ->willReturnMap($additionalData);
        $this->paymentDOMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($this->paymentMock);
        $this->paymentDOMock->expects(self::once())
            ->method('getOrder')
            ->willReturn($this->orderMock);
        self::assertEquals(
            $expectedResult,
            $this->builder->build($buildSubject)
        );
    }
}
