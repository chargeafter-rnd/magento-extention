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

namespace Chargeafter\Payment\Observer;

use Chargeafter\Payment\Registry\CurrentProductRegistry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProductInitAfter
 * @package Chargeafter\Payment\Observer
 */
class ProductInitAfter implements ObserverInterface
{
    /**
     * @var CurrentProductRegistry
     */
    private $currentProductRegistry;

    /**
     * ProductInitAfter constructor.
     * @param CurrentProductRegistry $currentProductRegistry
     */
    public function __construct(
        CurrentProductRegistry $currentProductRegistry
    ) {
        $this->currentProductRegistry = $currentProductRegistry;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /**
         * @var ProductInterface
         */
        $product = $observer->getData('product');
        $this->currentProductRegistry->set($product);
    }
}
