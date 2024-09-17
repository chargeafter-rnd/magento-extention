<?php

namespace Chargeafter\Payment\Model\Plugin;

use Chargeafter\Payment\Api\OrderTaxProcessInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Spi\OrderResourceInterface;
use Psr\Log\LoggerInterface;

class OrderTaxProcess implements OrderTaxProcessInterface
{
    /**
     * @var OrderResourceInterface
     */
    private $orderResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderTaxProcess constructor.
     * @param OrderResourceInterface $orderResource
     * @param LoggerInterface $logger
     */
    public function __construct(OrderResourceInterface $orderResource, LoggerInterface $logger)
    {
        $this->orderResource = $orderResource;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function reCalculate(OrderPaymentInterface $payment)
    {
        try {
            $order = $payment->getOrder();
            $orderTotal = $order->getTotalDue();

            $chargeId = $payment->getAdditionalInformation('chargeId');
            $chargeTotal = (float) $payment->getAdditionalInformation('chargeTotalAmount');

            if ($chargeId && $chargeTotal && ($chargeTotal < $orderTotal)) {
                $this->logger->info(sprintf(
                    "Starting tax re-calculation process for order %s, charge Id %s",
                    $order->getIncrementId(),
                    $chargeId
                ));

                $diffTotal = round($orderTotal - $chargeTotal, 2);
                if ($order->getTaxAmount() == $diffTotal) {
                    $updatedTax = 0;
                    $items = [];

                    foreach ($order->getAllItems() as $key => $item) {
                        if ($item->getTaxAmount()) {
                            $item->setTaxAmount($updatedTax);
                            $item->setTaxPercent(0);
                            $item->setBaseTaxAmount($updatedTax);
                        }

                        $items[$key] = $item;
                    }

                    $order->setItems($items);

                    $order->setShippingTaxAmount($updatedTax);
                    $order->setBaseShippingTaxAmount($updatedTax);

                    $order->setTaxAmount($updatedTax);
                    $order->setBaseTaxAmount($updatedTax);

                    $order->setGrandTotal($chargeTotal);
                    $order->setBaseGrandTotal($chargeTotal);

                    $order->addCommentToStatusHistory(
                        sprintf('Order tax changed. Transaction ID: "%s"', $chargeId)
                    );

                    $this->orderResource->save($order);

                    $this->logger->info(sprintf(
                        "Updated order tax for order %s, charge Id %s",
                        $order->getIncrementId(),
                        $chargeId
                    ));
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
