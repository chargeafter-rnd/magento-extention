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

namespace Chargeafter\Payment\Test\Unit\Model\Config\Source;

use Chargeafter\Payment\Model\Config\Source\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{

    public function testToOptionArray()
    {
        $expected = [
            [
                'value' => 'sandbox',
                'label' => 'Sandbox',
            ],
            [
                'value' => 'production',
                'label' => 'Production'
            ]
        ];
        $environment = new Environment();
        $actual = $environment->toOptionArray();

        self::assertSame($expected, $actual);
    }
}
