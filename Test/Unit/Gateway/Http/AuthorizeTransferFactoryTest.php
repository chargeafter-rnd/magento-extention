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

use Chargeafter\Payment\Gateway\Http\AuthorizeTransferFactory;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Chargeafter\Payment\Helper\ApiHelper;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthorizeTransferFactoryTest
 * @package Chargeafter\Payment\Test\Unit\Gateway\Http
 */
class AuthorizeTransferFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        $request = [
            'storeId'=>'1',
            'payload'=>[
                'confirmationToken'=>'123456',
                'merchantOrderId'=>'000000101',
            ]
        ];
        $url = '/payment/charges';
        $apiHelper = $this->createMock(ApiHelper::class);
        $apiHelper->expects($this->once())
            ->method('getApiUrl')
            ->with('/payment/charges', $request['storeId'])
            ->willReturn($url);
        $transferBuilder = $this->createMock(TransferBuilder::class);
        $transferBuilder->expects($this->once())
            ->method('setUri')
            ->with($url)
            ->willReturnSelf();
        $transferBuilder->expects($this->once())
            ->method('setMethod')
            ->with('POST')
            ->willReturnSelf();
        $privateKey = 'privateKey';
        $apiHelper->expects($this->once())
            ->method('getPrivateKey')
            ->with($request['storeId'])
            ->willReturn($privateKey);
        $transferBuilder->expects($this->once())
            ->method('setHeaders')
            ->with(
                [
                    'Authorization'=>'Bearer ' . $privateKey
                ]
            )
            ->willReturnSelf();
        $transferBuilder->expects($this->once())
            ->method('setBody')
            ->with($request['payload'])
            ->willReturnSelf();
        $transfer = $this->createMock(TransferInterface::class);

        $transferBuilder->expects($this->once())
            ->method('build')
            ->willReturnReference($transfer);
        $transferFactory = new AuthorizeTransferFactory($transferBuilder, $apiHelper);

        $this->assertSame($transfer, $transferFactory->create($request));
    }
}
