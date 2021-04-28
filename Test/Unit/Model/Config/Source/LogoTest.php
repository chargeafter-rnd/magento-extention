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

use Chargeafter\Payment\Model\Config\Source\Logo;
use PHPUnit\Framework\TestCase;

class LogoTest extends TestCase
{
    public function testToOptionArray()
    {
        $expected = [
            [
                'value' => '',
                'label' => 'No Image',
            ],
            [
                'value' => 'Btn_CA',
                'label' => 'Btn_CA'
            ],
            [
                'value' => 'Btn_CF',
                'label' => 'Btn_CF'
            ],
            [
                'value' => 'Btn_SatisFi',
                'label' => 'Btn_SatisFi'
            ]
        ];

        $logo = new Logo();

        $actual = $logo->toOptionArray();

        self::assertSame($expected, $actual);
    }
}
