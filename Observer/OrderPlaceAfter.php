<?php

namespace Chargeafter\Payment\Observer;

use Chargeafter\Payment\Api\TransactionTypeInterface;
use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Model\PaymentMethod;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Chargeafter\Payment\Api\OrderTaxProcessInterfaceFactory;
use Chargeafter\Payment\Api\InvoiceProcessInterfaceFactory;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var OrderTaxProcessInterfaceFactory
     */
    private $orderTaxProcessInterfaceFactory;

    /**
     * @var InvoiceProcessInterfaceFactory
     */
    private $invoiceProcessFactory;

    /**
     * @var ApiHelper
     */
    private $apiHelper;

    /**
     * OrderPlaceAfter constructor.
     * @param OrderTaxProcessInterfaceFactory $orderTaxProcessInterfaceFactory
     * @param InvoiceProcessInterfaceFactory $invoiceProcessFactory
     * @param ApiHelper $apiHelper
     */
    public function __construct(
        OrderTaxProcessInterfaceFactory $orderTaxProcessInterfaceFactory,
        InvoiceProcessInterfaceFactory $invoiceProcessFactory,
        ApiHelper $apiHelper
    ) {
        $this->orderTaxProcessInterfaceFactory = $orderTaxProcessInterfaceFactory;
        $this->invoiceProcessFactory = $invoiceProcessFactory;
        $this->apiHelper = $apiHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getId()) {
            $payment = $order->getPayment();

            if ($payment->getMethod() === PaymentMethod::CODE) {
                // Tax re-calculation
                $orderTaxProcess = $this->orderTaxProcessInterfaceFactory->create();
                $orderTaxProcess->reCalculate($payment);

                $quote = $observer->getData('quote');
                $storeId = $quote ? $quote->getStoreId() : null;
                $transactionType = $this->apiHelper->getTransactionType($storeId);

                if ($transactionType === TransactionTypeInterface::TRANSACTION_TYPE_CAPTURE) {
                    // Auto-capture
                    $invoiceProcess = $this->invoiceProcessFactory->create();
                    $invoiceProcess->invoice($payment);
                }
            }
        }
    }
}
