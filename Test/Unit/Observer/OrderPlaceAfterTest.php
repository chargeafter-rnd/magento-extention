<?php

namespace Chargeafter\Payment\Test\Unit\Observer;

use Chargeafter\Payment\Observer\OrderPlaceAfter;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Spi\OrderResourceInterface;
use PHPUnit\Framework\TestCase;

class OrderPlaceAfterTest extends TestCase
{
    public function testExecute()
    {
        $orderPlaceObserver = new OrderPlaceAfter(
            $this->createMock(OrderResourceInterface::class)
        );

        $payment = $this->createMock(OrderPaymentInterface::class);
        $payment->expects($this->once())
                ->method('getMethod')
                ->willReturn('chargeafter');
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
              ->method('getId')
              ->willReturn('any');
        $order->expects($this->once())
              ->method('getPayment')
              ->willReturn($payment);
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

        $event = new Event(['order' => $order]);
        $observer = new Observer(['event' => $event]);

        $orderPlaceObserver->execute($observer);
    }
}
