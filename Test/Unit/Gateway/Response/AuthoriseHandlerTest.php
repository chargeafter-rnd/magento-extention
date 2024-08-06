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

namespace Chargeafter\Payment\Test\Unit\Gateway\Response;

use Chargeafter\Payment\Gateway\Response\AuthoriseHandler;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class AuthoriseHandlerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testHandle()
    {
        $data = [
            'lender' => [
                'information' => [
                    'leaseId' => '0000021'
                ]
            ]
        ];

        $response = [
            'id' => '000001',
            'offer' => [
                'lender' => [
                    'name' => 'lender_name'
                ]
            ],
            'totalAmount' => 200
        ];

        $additionalInformation = [
            [$this->equalTo('lender'), $this->equalTo($response['offer']['lender']['name'])],
            [$this->equalTo('chargeId'), $this->equalTo($response['id'])],
            [$this->equalTo('chargeTotalAmount'), $this->equalTo($response['totalAmount'])],
            [$this->equalTo('leaseId'), $this->equalTo($data['lender']['information']['leaseId'])],
        ];

        $paymentMock = $this->createMock(Payment::class);
        $paymentMock->expects($this->exactly(count($additionalInformation)))
            ->method('setAdditionalInformation')
            ->withConsecutive(...$additionalInformation);
        $paymentMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->with('data')
            ->willReturn(json_encode($data));
        $paymentMock->expects($this->once())
            ->method('setTransactionId')
            ->with($response['id']);
        $paymentMock->expects($this->once())
            ->method('setIsTransactionClosed')
            ->with(false);

        $paymentDoMock = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDoMock->expects(self::once())
            ->method('getPayment')
            ->willReturn($paymentMock);

        $handler = new AuthoriseHandler();
        $handler->handle([ 'payment' => $paymentDoMock ], $response);
    }
}
