<?php

namespace Chargeafter\Payment\Test\Unit\Model\Plugin;

use Chargeafter\Payment\Model\Plugin\InvoiceProcess;
use Magento\Framework\DB\Transaction;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class InvoiceProcessTest extends TestCase
{
    /**
     * @var Transaction|(Transaction&object&MockObject)|(Transaction&MockObject)|(object&MockObject)|MockObject
     */
    private $transactionMock;

    /**
     * @var InvoiceProcess
     */
    private $invoiceProcess;

    protected function setUp(): void
    {
        $this->transactionMock = $this->createMock(Transaction::class);

        $transactionFactoryMock = $this->getMockBuilder(TransactionFactory::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();
        $transactionFactoryMock->expects($this->any())
                               ->method('create')
                               ->willReturn($this->transactionMock);

        $this->invoiceProcess = new InvoiceProcess(
            $transactionFactoryMock,
            $this->getMockForAbstractClass(LoggerInterface::class)
        );
    }

    public function testInvoice()
    {
        $invoice = $this->createMock(Invoice::class);
        $invoice->expects($this->once())
                ->method('register')
                ->willReturnSelf();

        $order = $this->createMock(Order::class);
        $order->expects($this->exactly(2))
            ->method('getIncrementId')
            ->willReturn('any');
        $order->expects($this->once())
              ->method('prepareInvoice')
              ->willReturn($invoice);

        $payment = $this->createMock(Order\Payment::class);
        $payment->expects($this->once())
            ->method('getOrder')
            ->willReturn($order);

        $this->transactionMock->expects($this->exactly(2))
                              ->method('addObject')
                              ->withAnyParameters()
                              ->willReturnSelf();
        $this->transactionMock->expects($this->once())
                              ->method('save')
                              ->willReturnSelf();

        $this->invoiceProcess->invoice($payment);
    }
}
