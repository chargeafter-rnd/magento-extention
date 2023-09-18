<?php

namespace Chargeafter\Payment\Model\Plugin;

use Magento\Framework\DB\TransactionFactory;
use Chargeafter\Payment\Api\InvoiceProcessInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Invoice;
use Psr\Log\LoggerInterface;

class InvoiceProcess implements InvoiceProcessInterface
{
    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * InvoiceProcess constructor.
     * @param TransactionFactory $transactionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(TransactionFactory $transactionFactory, LoggerInterface $logger)
    {
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function invoice(OrderPaymentInterface $payment)
    {
        try {
            $order = $payment->getOrder();

            $this->logger->info(
                sprintf("Starting auto-capture process for order %s", $order->getIncrementId())
            );

            $invoice = $order->prepareInvoice();
            $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
            $invoice->register();

            $transaction = $this->transactionFactory->create();
            $transaction->addObject($invoice);
            $transaction->addObject($order);
            $transaction->save();

            $this->logger->info(
                sprintf("Order %s automatically captured", $order->getIncrementId())
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
