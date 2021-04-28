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

use Chargeafter\Payment\Gateway\Http\CaptureTransferFactory;
use Chargeafter\Payment\Helper\ApiHelper;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\TestCase;

class CaptureTransferFactoryTest extends TestCase
{

    public function testCreate()
    {
        $request = [
            'storeId'=>1,
            'chargeId'=>123456,
            'payload'=>[
                'amount'=>10.35,
            ],
        ];
        $url = "/post-sale/charges/{$request['chargeId']}/settles";
        $apiHelper = $this->createMock(ApiHelper::class);
        $apiHelper->expects($this->once())
            ->method('getApiUrl')
            ->with($url, $request['storeId'])
            ->willReturnArgument(0);
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
        $transferFactory = new CaptureTransferFactory($transferBuilder, $apiHelper);

        $this->assertSame($transfer, $transferFactory->create($request));
    }
}
