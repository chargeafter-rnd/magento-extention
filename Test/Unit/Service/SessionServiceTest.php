<?php
/**
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      eduard.samoilenko@chargeafter.com
 */

namespace Chargeafter\Payment\Test\Unit\Service;

use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Service\SessionService;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class SessionServiceTest
 * @package Chargeafter\Payment\Test\Unit\Service
 */
class SessionServiceTest extends TestCase
{
    /**
     * @var MockBuilder
     */
    private $clientMock;

    /**
     * @var MockBuilder
     */
    private $storeModelMock;

    /**
     * @var MockBuilder
     */
    private $clientFactoryMock;

    /**
     * @var MockBuilder
     */
    private $storeManagerFactoryMock;

    /**
     * @var MockBuilder
     */
    private $apiHelperMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->getMockBuilder(ClientInterface::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->clientMock->expects($this->any())
                         ->method('setHeaders')
                         ->willReturnSelf();
        $this->clientMock->expects($this->any())
                         ->method('getStatus')
                         ->willReturn(200);
        $this->clientMock->expects($this->any())
                         ->method('getHeaders')
                         ->willReturn([]);

        $this->storeModelMock = $this->createMock(Store::class);
        $this->storeModelMock->expects($this->any())
                             ->method('getId')
                             ->willReturn(2);

        $this->clientFactoryMock = $this->createMock(ClientFactory::class);
        $this->clientFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->clientMock);

        $this->storeManagerFactoryMock = $this->createMock(StoreManagerInterface::class);
        $this->storeManagerFactoryMock->expects($this->any())
            ->method('getStore')
            ->willReturn($this->storeModelMock);

        $this->apiHelperMock = $this->createMock(ApiHelper::class);
        $this->apiHelperMock->expects($this->any())
                            ->method('getCdnUrl')
                            ->willReturn('cdnUrl');
    }

    /**
     * @dataProvider testCreateProvider
     */
    public function testCreateSession($sessionId, $response)
    {
        $this->clientMock->expects($this->any())
                         ->method('getBody')
                         ->willReturn($response);

        $sessionService = new SessionService(
            $this->clientFactoryMock,
            $this->storeManagerFactoryMock,
            $this->apiHelperMock
        );
        $actual = $sessionService->createSession();

        $this->assertEquals($sessionId, $actual);
    }

    public function testCreateProvider(): array
    {
        return[
            [
                'sessionId' => '8p2NBJcSfaLc7Tgy',
                'response' => "{\"id\":\"8p2NBJcSfaLc7Tgy\"}"
            ],
            [
                'sessionId' => null,
                'response' => "{}",
            ]
        ];
    }

    /**
     * @dataProvider testGetMerchantByProvider
     */
    public function testGetMerchantBySession($sessionId, $response, $merchantId)
    {
        $this->clientMock->expects($this->any())
            ->method('getBody')
            ->willReturn($response);

        $sessionService = new SessionService(
            $this->clientFactoryMock,
            $this->storeManagerFactoryMock,
            $this->apiHelperMock
        );
        $actual = $sessionService->getMerchantBySession($sessionId);

        $this->assertEquals($merchantId, $actual);
    }

    public function testGetMerchantByProvider(): array
    {
        return[
            [
                'sessionId' => '8p2NBJcSfaLc7Tgy',
                'response' => "{\"data\": {\"merchantId\": \"Q7UcVPAqbzQjmGnw\"} }",
                'merchantId' => 'Q7UcVPAqbzQjmGnw'
            ],
            [
                'sessionId' => null,
                'response' => "{}",
                'merchantId' => null
            ]
        ];
    }
}
