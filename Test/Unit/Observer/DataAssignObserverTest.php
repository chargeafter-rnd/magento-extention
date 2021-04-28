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

use Chargeafter\Payment\Observer\DataAssignObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class DataAssignObserverTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testExecute()
    {
        $dataAssignObserver = new DataAssignObserver();
        $data = new DataObject(['additional_data'=>['token'=>'testToken','data'=>'testData']]);
        $paymentInfo = $this->createMock(InfoInterface::class);
        $paymentInfo->expects($this->exactly(2))
            ->method('setAdditionalInformation')
            ->withConsecutive(
                ['token','testToken'],
                ['data','testData']
            );
        $event = new Event(['data'=>$data,'payment_model'=>$paymentInfo]);
        $observer = new Observer(['event'=>$event]);

        $dataAssignObserver->execute($observer);
    }
}
