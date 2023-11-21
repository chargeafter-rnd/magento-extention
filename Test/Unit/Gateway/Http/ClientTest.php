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
use Laminas\Http\Response;
use Laminas\Http\Client as LaminasClient;
use Laminas\Http\ClientFactory as LaminasClientFactory;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\TestCase;

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

        $httpClient = $this->createMock(LaminasClient::class);
        $clientFactory = $this->createMock(LaminasClientFactory::class);

        $clientFactory->expects($this->once())
            ->method('create')
            ->willReturnReference($httpClient);

        $httpClient->expects($this->once())
            ->method('setUri')
            ->with($url);

        $httpClient->expects($this->once())
            ->method('setHeaders')
            ->with($headers)
            ->willReturnSelf();

        $httpClient->expects($this->once())
            ->method('setMethod')
            ->with($method)
            ->willReturnSelf();

        $transferObject->expects($method === 'POST' ? $requestBody ? $this->exactly(2) : $this->once() : $this->never())
            ->method('getBody')
            ->willReturn($requestBody);

        $httpClient->expects($method === 'POST' && $requestBody ? $this->once() : $this->never())
            ->method('setRawBody')
            ->with(json_encode($requestBody))
            ->willReturnSelf();

        if (key_exists('accept', $headers)) {
            $httpClient->expects($this->once())
                ->method('setEncType')
                ->with($headers['accept'])
                ->willReturnSelf();
        }

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($responseBody);

        $httpClient->expects($this->once())
            ->method('send')
            ->willReturnReference($response);

        $client = new Client($clientFactory);

        $this->assertSame(json_decode($responseBody, true), $client->placeRequest($transferObject));
    }

    public function additionProvider(): array
    {
        return [
            [
                '/payment/charges',
                [
                    'Authorization' => 'Bearer privateKey'
                ],
                'POST',
                [
                    'payload' => 'payload'
                ],
                json_encode(['body']),
            ],
            [
                '/payment/charges',
                [
                    'Authorization' => 'Bearer privateKey',
                    'accept' => 'application/json'
                ],
                'GET',
                [
                    'payload' => 'payload'
                ],
                json_encode(['body']),
            ],
            [
                '/payment/charges',
                [
                    'Authorization' => 'Bearer privateKey'
                ],
                'POST',
                null,
                json_encode(['body']),
            ],
        ];
    }
}
