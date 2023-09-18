<?php

namespace Chargeafter\Payment\Test\Unit\Model\Plugin;

use Chargeafter\Payment\Model\Plugin\OrderTaxProcess;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Spi\OrderResourceInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class OrderTaxProcessTest extends TestCase
{
    /**
     * @var OrderResourceInterface|(OrderResourceInterface&object&\PHPUnit\Framework\MockObject\MockObject)|(OrderResourceInterface&\PHPUnit\Framework\MockObject\MockObject)|(object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderResourceMock;

    /**
     * @var OrderTaxProcess
     */
    private $orderTaxProcess;

    protected function setUp(): void
    {
        $this->orderResourceMock = $this->getMockForAbstractClass(OrderResourceInterface::class);
        $loggerInterface = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->orderTaxProcess = new OrderTaxProcess(
            $this->orderResourceMock,
            $loggerInterface
        );
    }

    public function testReCalculate()
    {
        $payment = $this->createMock(Order\Payment::class);
        $payment->expects($this->exactly(2))
            ->method('getAdditionalInformation')
            ->willReturnCallback(function ($name) {
                if ($name === 'chargeId') {
                    return 'any_charge';
                }

                if ($name === 'chargeTotalAmount') {
                    return 747.2;
                }

                return null;
            });

        $order = $this->createMock(Order::class);
        $order->expects($this->exactly(2))
            ->method('getIncrementId')
            ->willReturn('any');
        $order->expects($this->once())
            ->method('getTotalDue')
            ->willReturn(784.56);
        $order->expects($this->once())
            ->method('getTaxAmount')
            ->willReturn(37.36);
        $order->expects($this->once())
            ->method('setBaseTaxAmount')
            ->with(0)
            ->willReturn($order);
        $order->expects($this->once())
            ->method('setTaxAmount')
            ->with(0)
            ->willReturn($order);
        $order->expects($this->once())
            ->method('setBaseGrandTotal')
            ->with(747.2)
            ->willReturn($order);
        $order->expects($this->once())
            ->method('setGrandTotal')
            ->with(747.2)
            ->willReturn($order);

        $payment->expects($this->once())
                ->method('getOrder')
                ->willReturn($order);

        $this->orderResourceMock->expects($this->once())
                                ->method('save')
                                ->willReturnSelf();

        $this->orderTaxProcess->reCalculate($payment);
    }

    public function testReCalculateWithSameTotal()
    {
        $payment = $this->createMock(Order\Payment::class);
        $payment->expects($this->exactly(2))
            ->method('getAdditionalInformation')
            ->willReturnCallback(function ($name) {
                if ($name === 'chargeId') {
                    return 'any_charge';
                }

                if ($name === 'chargeTotalAmount') {
                    return 747.2;
                }

                return null;
            });

        $order = $this->createMock(Order::class);
        $order->expects($this->once())
              ->method('getTotalDue')
              ->willReturn(747.2);

        $payment->expects($this->once())
                ->method('getOrder')
                ->willReturn($order);

        $this->orderTaxProcess->reCalculate($payment);
    }
}
