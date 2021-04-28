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
     * @param $logo
     * @param $expected
     * @throws ReflectionException
     * @dataProvider dataProvider
     */
    public function testGetConfig($logo, $expected)
    {
        $method = $this->createMock(MethodInterface::class);
        $method->expects($this->exactly(2))
            ->method('getConfigData')
            ->willReturnMap([
                ['description',null,'description'],
                ['logo',null,$logo]
            ]);
        $assetRepo = $this->createMock(Repository::class);
        $assetRepo->expects($logo ? $this->once() : $this->never())
            ->method('getUrl')
            ->with("Chargeafter_Payment::images/" . $logo . ".svg")
            ->willReturnArgument(0);
        $helper = $this->createMock(ApiHelper::class);
        $helper->expects($this->once())
            ->method('getCdnUrl')
            ->willReturn('cdnUrl');
        $helper->expects($this->once())
            ->method('getPublicKey')
            ->willReturn('publicKey');
        $configProvider = new ConfigProvider($method, $assetRepo, $helper);
        $actual = $configProvider->getConfig();
        self::assertEquals($expected, $actual);
    }

    public function dataProvider(): array
    {
        return[
            [
                'logo'=>'logo',
                'expected'=>[
                    'payment'=>[
                        'chargeafter'=>[
                            'description'=>'description',
                            'logo'=> "Chargeafter_Payment::images/logo.svg",
                            'cdnUrl' => 'cdnUrl',
                            'publicKey' => 'publicKey'
                        ]
                    ]
                ]
            ],
            [
                'logo'=>null,
                'expected'=>[
                    'payment'=>[
                        'chargeafter'=>[
                            'description'=>'description',
                            'logo'=> null,
                            'cdnUrl' => 'cdnUrl',
                            'publicKey' => 'publicKey'
                        ]
                    ]
                ]
            ]
        ];
    }
}
