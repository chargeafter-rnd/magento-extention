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
use Magento\Payment\Model\MethodInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    /**
     * @param $description
     * @param $cdnUrl
     * @param $publicKey
     * @param $storeId
     * @param $customerDataUpdateActive
     * @param $shippingEqualsBilling
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testGetConfig($description, $cdnUrl, $publicKey, $storeId, $customerDataUpdateActive, $shippingEqualsBilling, $expected)
    {
        $method = $this->createMock(MethodInterface::class);
        $method->expects($this->once())
            ->method('getConfigData')
            ->willReturnMap([
                ['description', null, $description],
            ]);

        $helper = $this->createMock(ApiHelper::class);
        $helper->expects($this->any())
            ->method('getCdnUrl')
            ->willReturn($cdnUrl);
        $helper->expects($this->once())
            ->method('getPublicKey')
            ->willReturn($publicKey);
        $helper->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $helper->expects($this->once())
            ->method('shouldUpdateConsumerData')
            ->willReturn($customerDataUpdateActive);
        $helper->expects($this->once())
            ->method('shouldBeShippingEqualsBilling')
            ->willReturn($shippingEqualsBilling);

        $configProvider = new ConfigProvider($method, $helper);
        $actual = $configProvider->getConfig();

        self::assertEquals($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [
                'description' => 'description',
                'cdn_url' => 'cdnUrl',
                'public_key' => 'publicKey',
                'store_id' => 'storeId',
                'customer_data_update_active' => true,
                'bill_to_equal_ship_to' => true,
                'expected' => [
                    'payment' => [
                        'chargeafter' => [
                            'description' => 'description',
                            'cdnUrl' => 'cdnUrl',
                            'publicKey' => 'publicKey',
                            'storeId' => 'storeId',
                            'shouldUpdateConsumerData' => true,
                            'shouldBeSameCustomerBillingAddress' => true
                        ]
                    ]
                ]
            ],
            [
                'description' => 'description',
                'cdn_url' => 'cdnUrl',
                'public_key' => 'publicKey',
                'store_id' => null,
                'customer_data_update_active' => false,
                'bill_to_equal_ship_to' => false,
                'expected' => [
                    'payment' => [
                        'chargeafter' => [
                            'description' => 'description',
                            'cdnUrl' => 'cdnUrl',
                            'publicKey' => 'publicKey',
                            'storeId' => null,
                            'shouldUpdateConsumerData' => false,
                            'shouldBeSameCustomerBillingAddress' => false
                        ]
                    ]
                ]
            ]
        ];
    }
}
