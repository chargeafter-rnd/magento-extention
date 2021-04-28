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

namespace Chargeafter\Payment\Test\Unit\Helper;

use Chargeafter\Payment\Helper\ApiHelper;
use Magento\Payment\Gateway\ConfigInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ApiHelperTest extends TestCase
{
    private $config;
    private $helper;

    /**
     * @throws ReflectionException
     */
    protected function setUp()
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->helper = new ApiHelper($this->config);
    }

    /**
     * @param string $environment
     * @param string $expected
     * @dataProvider dataProviderTestGetApiUrl
     */
    public function testGetApiUrl(string $environment, string $expected)
    {
        $this->config->expects($this->once())
            ->method('getValue')
            ->with('environment')
            ->willReturn($environment);

        $actual = $this->helper->getApiUrl();

        self::assertSame($expected, $actual);

    }

    /**
     * @return string[][]
     */
    public function dataProviderTestGetApiUrl(): array
    {
        return[
            [
                'environment'=>'sandbox',
                'expected'=>'https://api-sandbox.ca-dev.co/v1'
            ],
            [
                'environment'=>'production',
                'expected'=>'https://api.chargeafter.com/v1'
            ]
        ];
    }

    /**
     * @param string $environment
     * @param string $expected
     * @dataProvider dataProviderTestGetCdnUrl
     */
    public function testGetCdnUrl(string $environment, string $expected)
    {
        $this->config->expects($this->once())
            ->method('getValue')
            ->with('environment')
            ->willReturn($environment);

        $actual = $this->helper->getCdnUrl();

        self::assertSame($expected, $actual);
    }

    public function dataProviderTestGetCdnUrl(): array
    {
        return[
            [
                'environment'=>'sandbox',
                'expected'=>'https://cdn-sandbox.ca-dev.co'
            ],
            [
                'environment'=>'production',
                'expected'=>'https://cdn.chargeafter.com'
            ]
        ];
    }

    /**
     * @param string $environment
     * @param string $expected
     * @dataProvider dataProviderTestGetPrivateKey
     */
    public function testGetPrivateKey(string $environment, string $expected)
    {
        $this->config->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnMap([
                ['environment', null, $environment],
                ['sandbox_private_key', null, 'sandbox_private_key'],
                ['production_private_key', null, 'production_private_key']
            ]);

        $actual = $this->helper->getPrivateKey();

        self::assertSame($expected, $actual);
    }

    public function dataProviderTestGetPrivateKey(): array
    {
        return [
            [
                'environment'=>'sandbox',
                'expected'=>'sandbox_private_key'
            ],
            [
                'environment'=>'production',
                'expected'=>'production_private_key'
            ]
        ];
    }

    /**
     * @param string $environment
     * @param string $expected
     * @dataProvider dataProviderTestGetPublicKey
     */
    public function testGetPublicKey(string $environment, string $expected)
    {
        $this->config->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnMap([
                ['environment', null, $environment],
                ['sandbox_public_key', null, 'sandbox_public_key'],
                ['production_public_key', null, 'production_public_key']
            ]);

        $actual = $this->helper->getPublicKey();

        self::assertSame($expected, $actual);
    }

    public function dataProviderTestGetPublicKey(): array
    {
        return [
            [
                'environment'=>'sandbox',
                'expected'=>'sandbox_public_key'
            ],
            [
                'environment'=>'production',
                'expected'=>'production_public_key'
            ]
        ];
    }
}
