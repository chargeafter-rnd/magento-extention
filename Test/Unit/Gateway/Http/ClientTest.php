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

namespace Chargeafter\Payment\Test\Unit\Gateway\Http;

use Chargeafter\Payment\Gateway\Http\Client;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\TestCase;
use Zend_Http_Response;

class ClientTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     * @param $url
     * @param $headers
     * @param $method
     * @param $requestBody
     * @param $responseBody
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function testPlaceRequest($url, $headers, $method, $requestBody, $responseBody)
    {
        $transferObject = $this->createMock(TransferInterface::class);
        $transferObject->expects($this->once())
            ->method('getUri')
            ->willReturn($url);
        $transferObject->expects($this->once())
            ->method('getHeaders')
            ->willReturn($headers);
        $transferObject->expects($this->exactly(2))
            ->method('getMethod')
            ->willReturn($method);

        $zendClient = $this->createMock(ZendClient::class);
        $clientFactory = $this->createMock(ZendClientFactory::class);

        $clientFactory->expects($this->once())
            ->method('create')
            ->with(['uri'=>$url])
            ->willReturnReference($zendClient);

        $zendClient->expects($this->once())
            ->method('setHeaders')
            ->with($headers)
            ->willReturnSelf();

        $zendClient->expects($this->once())
            ->method('setMethod')
            ->with($method)
            ->willReturnSelf();
        $transferObject->expects($method==='POST' ? $requestBody ? $this->exactly(2) : $this->once() : $this->never())
            ->method('getBody')
            ->willReturn($requestBody);
        $zendClient->expects($method==='POST' && $requestBody ? $this->once() : $this->never())
            ->method('setRawData')
            ->with(json_encode($requestBody), 'application/json')
            ->willReturnSelf();
        $response = $this->createMock(Zend_Http_Response::class);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($responseBody);

        $zendClient->expects($this->once())
            ->method('request')
            ->willReturnReference($response);

        $client = new Client($clientFactory);

        $this->assertSame(json_decode($responseBody, true), $client->placeRequest($transferObject));
    }

    public function additionProvider(): array
    {
        return [
            [
                '/payment/charges',
                ['Authorization'=>'Bearer privateKey'],
                'POST',
                ['payload'=>'payload'],
                json_encode(['body']),
            ],
            [
                '/payment/charges',
                ['Authorization'=>'Bearer privateKey'],
                'GET',
                ['payload'=>'payload'],
                json_encode(['body']),
            ],
            [
                '/payment/charges',
                ['Authorization'=>'Bearer privateKey'],
                'POST',
                null,
                json_encode(['body']),
            ],
        ];
    }
}
