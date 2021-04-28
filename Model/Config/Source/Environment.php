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

namespace Chargeafter\Payment\Model\Config\Source;

/**
 * Class Environment
 * @package Chargeafter\Payment\Model\Config\Source
 */
class Environment implements \Magento\Framework\Data\OptionSourceInterface
{
    const ENVIRONMENT_SANDBOX = 'sandbox';
    const ENVIRONMENT_PRODUCTION = 'production';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_SANDBOX,
                'label' => 'Sandbox',
            ],
            [
                'value' => self::ENVIRONMENT_PRODUCTION,
                'label' => 'Production'
            ]
        ];
    }
}
