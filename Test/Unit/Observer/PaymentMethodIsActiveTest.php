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

namespace Chargeafter\Payment\Test\Unit\Observer;

use Chargeafter\Payment\Observer\PaymentMethodIsActive;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\MethodInterface;
use Chargeafter\Payment\Helper\ApiHelper;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\TestCase;

class PaymentMethodIsActiveTest extends TestCase
{
    public function testExecute()
    {
        $storeId = 1;
        $publicKey = 123456;
        $apiHelper = $this->createMock(ApiHelper::class);
        $apiHelper->expects($this->once())
            ->method('getPublicKey')
            ->with($this->equalTo($storeId))
            ->willReturn($publicKey);
        $apiHelper->expects($this->once())
            ->method('getPrivateKey')
            ->with($this->equalTo($storeId))
            ->willReturn(null);
        $dataAssignObserver = new PaymentMethodIsActive($apiHelper);
        $quote = $this->createMock(Quote::class);
        $quote->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $result = new DataObject();
        $methodInstance = $this->createMock(MethodInterface::class);
        $methodInstance->expects($this->once())
            ->method('getCode')
            ->willReturn('chargeafter');
        $observer = new Observer(['method_instance'=>$methodInstance,'quote'=>$quote,'result'=>$result]);

        $dataAssignObserver->execute($observer);

        $this->assertFalse($result->getData('is_available'));
    }
}
