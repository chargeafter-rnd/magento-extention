<?php

namespace Chargeafter\Payment\Test\Unit\Model\Config\Source;

use Chargeafter\Payment\Model\Config\Source\Environment;
use Chargeafter\Payment\Model\Config\Source\TransactionType;
use PHPUnit\Framework\TestCase;

class TransactionTypeTest extends TestCase
{

    public function testToOptionArray()
    {
        $expected = [
            [
                'value' => 'authorization',
                'label' => 'Authorization',
            ],
            [
                'value' => 'capture',
                'label' => 'Capture'
            ]
        ];
        $environment = new TransactionType();
        $actual = $environment->toOptionArray();

        self::assertSame($expected, $actual);
    }
}
