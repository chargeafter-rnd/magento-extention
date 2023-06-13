<?php

namespace Chargeafter\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Spi\OrderResourceInterface;

class OrderPlaceAfter implements ObserverInterface
{
    private $orderResource;

    public function __construct(OrderResourceInterface $orderResource)
    {
        $this->orderResource = $orderResource;
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getId()) {

            $payment = $order->getPayment();
            if ($payment->getMethod() === 'chargeafter') {

                $chargeId = $payment->getAdditionalInformation('chargeId');

                $chargeTotal = (float) $payment->getAdditionalInformation('chargeTotalAmount');
                $orderTotal = $order->getTotalDue();

                if ($chargeId && $chargeTotal && ($chargeTotal < $orderTotal)) {
                    $diffTotal = round($orderTotal - $chargeTotal,2);
                    if ($order->getTaxAmount() == $diffTotal) {
                        $order->setBaseTaxAmount(0)
                              ->setTaxAmount(0)
                              ->setBaseGrandTotal($chargeTotal)
                              ->setGrandTotal($chargeTotal);

                        $order->addCommentToStatusHistory(
                            "Updated tax status as tax-free online. Transaction ID: \"{$chargeId}\""
                        );

                        $this->orderResource->save($order);
                    }
                }
            }
        }
    }
}
