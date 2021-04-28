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

namespace Chargeafter\Payment\Registry;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class CurrentProductRegistry
 * @package Chargeafter\Payment\Registry
 */
class CurrentProductRegistry
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @return ProductInterface||null
     */
    public function get(): ?ProductInterface
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     */
    public function set(ProductInterface $product): void
    {
        $this->product = $product;
    }
}
