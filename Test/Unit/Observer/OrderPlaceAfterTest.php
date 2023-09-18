<?php

namespace Chargeafter\Payment\Test\Unit\Observer;

use Chargeafter\Payment\Api\InvoiceProcessInterface;
use Chargeafter\Payment\Api\InvoiceProcessInterfaceFactory;
use Chargeafter\Payment\Api\OrderTaxProcessInterface;
use Chargeafter\Payment\Api\OrderTaxProcessInterfaceFactory;
use Chargeafter\Payment\Api\TransactionTypeInterface;
use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Observer\OrderPlaceAfter;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;

class OrderPlaceAfterTest extends TestCase
{
    /**
     * @var OrderTaxProcessInterface|(OrderTaxProcessInterface&object&\PHPUnit\Framework\MockObject\MockObject)|(OrderTaxProcessInterface&\PHPUnit\Framework\MockObject\MockObject)|(object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderTaxProcessMock;

    /**
     * @var InvoiceProcessInterface|(InvoiceProcessInterface&object&\PHPUnit\Framework\MockObject\MockObject)|(InvoiceProcessInterface&\PHPUnit\Framework\MockObject\MockObject)|(object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
     */
    private $invoiceProcessMock;

    /**
     * @var ApiHelper|(ApiHelper&object&\PHPUnit\Framework\MockObject\MockObject)|(ApiHelper&\PHPUnit\Framework\MockObject\MockObject)|(object&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apiHelperMock;

    /**
     * @var OrderPlaceAfter
     */
    private $orderPlaceAfter;

    protected function setUp(): void
    {
        $this->orderTaxProcessMock = $this->getMockForAbstractClass(OrderTaxProcessInterface::class);
        $this->invoiceProcessMock = $this->getMockForAbstractClass(InvoiceProcessInterface::class);

        $orderTaxProcessFactoryMock = $this->getMockBuilder(OrderTaxProcessInterfaceFactory::class)
                                          ->disableOriginalConstructor()
                                          ->getMock();

        $invoiceProcessFactoryMock = $this->getMockBuilder(InvoiceProcessInterfaceFactory::class)
                                         ->disableOriginalConstructor()
                                         ->getMock();

        $orderTaxProcessFactoryMock->expects($this->any())
                                   ->method('create')
                                   ->willReturn($this->orderTaxProcessMock);

        $invoiceProcessFactoryMock->expects($this->any())
                                  ->method('create')
                                  ->willReturn($this->invoiceProcessMock);

        $this->apiHelperMock = $this->createMock(ApiHelper::class);

        $this->orderPlaceAfter = new OrderPlaceAfter(
            $orderTaxProcessFactoryMock,
            $invoiceProcessFactoryMock,
            $this->apiHelperMock
        );
    }

    public function testExecute()
    {
        $this->orderTaxProcessMock->expects($this->once())
                                  ->method('reCalculate')
                                  ->willReturn(true);

        $payment = $this->createMock(OrderPaymentInterface::class);
        $payment->expects($this->once())
                ->method('getMethod')
                ->willReturn('chargeafter');

        $order = $this->createMock(Order::class);
        $order->expects($this->once())
              ->method('getId')
              ->willReturn('any');
        $order->expects($this->once())
              ->method('getPayment')
              ->willReturn($payment);

        $this->apiHelperMock->expects($this->once())
                            ->method('getTransactionType')
                            ->willReturn(null);

        $event = new Event(['order' => $order]);
        $observer = new Observer(['event' => $event]);

        $this->orderPlaceAfter->execute($observer);
    }

    public function testExecuteWithAutoCapture()
    {
        $this->orderTaxProcessMock->expects($this->once())
                                  ->method('reCalculate')
                                  ->willReturn(true);

        $payment = $this->createMock(OrderPaymentInterface::class);
        $payment->expects($this->once())
                ->method('getMethod')
                ->willReturn('chargeafter');

        $order = $this->createMock(Order::class);
        $order->expects($this->once())
              ->method('getId')
              ->willReturn('any');
        $order->expects($this->once())
              ->method('getPayment')
              ->willReturn($payment);

        $storeId = 123;
        $quote = $this->createMock(Quote::class);
        $quote->expects($this->once())
              ->method('getStoreId')
              ->willReturn($storeId);

        $this->apiHelperMock->expects($this->once())
                            ->method('getTransactionType')
                            ->with($storeId)
                            ->willReturn(TransactionTypeInterface::TRANSACTION_TYPE_CAPTURE);

        $this->invoiceProcessMock->expects($this->once())
                                 ->method('invoice');

        $event = new Event(['order' => $order]);
        $observer = new Observer(['event' => $event, 'quote' => $quote]);

        $this->orderPlaceAfter->execute($observer);
    }
}
