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

namespace Chargeafter\Payment\Test\Unit\Model\Ui;

use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Model\Ui\ConfigProvider;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Model\MethodInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ConfigProviderTest extends TestCase
{
    /**
     * @param $expected
     * @dataProvider dataProvider
     */
    public function testGetConfig($expected)
    {
        $method = $this->createMock(MethodInterface::class);
        $method->expects($this->once())
            ->method('getConfigData')
            ->willReturnMap([
                ['description',null,'description'],
            ]);

        $helper = $this->createMock(ApiHelper::class);
        $helper->expects($this->any())
            ->method('getCdnUrl')
            ->willReturn('cdnUrl');
        $helper->expects($this->once())
            ->method('getPublicKey')
            ->willReturn('publicKey');

        $configProvider = new ConfigProvider($method, $helper);
        $actual = $configProvider->getConfig();

        self::assertEquals($expected, $actual);
    }

    public function dataProvider(): array
    {
        return[
            [
                'expected'=>[
                    'payment'=>[
                        'chargeafter'=>[
                            'description'=>'description',
                            'cdnUrl' => 'cdnUrl',
                            'publicKey' => 'publicKey'
                        ]
                    ]
                ]
            ],
            [
                'expected'=>[
                    'payment'=>[
                        'chargeafter'=>[
                            'description'=>'description',
                            'cdnUrl' => 'cdnUrl',
                            'publicKey' => 'publicKey'
                        ]
                    ]
                ]
            ]
        ];
    }
}
