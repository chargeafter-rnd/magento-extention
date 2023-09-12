<?php

namespace Chargeafter\Payment\Model\Plugin;

use Chargeafter\Payment\Api\OrderTaxProcessInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
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
            /** @var Order\Payment $order */
            $order = $payment->getOrder();
            $chargeId = $payment->getAdditionalInformation('chargeId');

            $chargeTotal = (float) $payment->getAdditionalInformation('chargeTotalAmount');
            $orderTotal = $order->getTotalDue();

            if ($chargeId && $chargeTotal && ($chargeTotal < $orderTotal)) {
                $this->logger->info(sprintf(
                    "Starting tax re-calculation process for order %s. Charge Id: %s",
                    $order->getIncrementId(),
                    $chargeId
                ));

                $diffTotal = round($orderTotal - $chargeTotal, 2);
                if ($order->getTaxAmount() == $diffTotal) {
                    $order->setBaseTaxAmount(0)
                        ->setTaxAmount(0)
                        ->setBaseGrandTotal($chargeTotal)
                        ->setGrandTotal($chargeTotal);

                    $order->addCommentToStatusHistory(
                        "Updated tax status as tax-free online. Transaction ID: \"{$chargeId}\""
                    );

                    $this->orderResource->save($order);

                    $this->logger->info(sprintf(
                        "Updated order tax for order %s, charge Id: %s",
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
