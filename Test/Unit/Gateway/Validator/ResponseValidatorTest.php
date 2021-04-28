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

namespace Chargeafter\Payment\Test\Unit\Gateway\Validator;

use Chargeafter\Payment\Gateway\Validator\ResponseValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\Result;

use PHPUnit\Framework\TestCase;

class ResponseValidatorTest extends TestCase
{
    /**
     * @param array $validationSubject
     * @param bool $isValid
     * @param array $messages
     * @param array $errorCodes
     *
     * @dataProvider dataProviderTestValidate
     */
    public function testValidate(array $validationSubject, bool $isValid, array $messages, array $errorCodes)
    {
        $result = new Result($isValid, $messages);
        $resultFactory = $this->createMock(ResultInterfaceFactory::class);
        $resultFactory->expects($this->once())
            ->method('create')
            ->with([
                'isValid' => $isValid,
                'failsDescription' => $messages,
                'errorCodes' => $errorCodes
            ])
            ->willReturn($result);
        $responseValidator = new ResponseValidator($resultFactory);
        $actual = $responseValidator->validate($validationSubject);
        self::assertEquals($result, $actual);
    }

    /**
     * @return array
     */
    public function dataProviderTestValidate(): array
    {
        return [
            'Invalid response with message'=>[
                'validationSubject'=>[
                    'response'=> [
                        'code'=>123,
                        'message'=>'Error Code 123'
                    ]
                ],
                'isValid'=>false,
                'messages'=>[
                    'Error Code 123'
                ],
                'errorCodes'=>[]
            ],
            'Invalid response without message'=>[
                'validationSubject'=>[
                    'response'=> [
                        'code'=>123,
                    ]
                ],
                'isValid'=>false,
                'messages'=>[
                    'Braintree error response.'
                ],
                'errorCodes'=>[]
            ],
            'Valid response'=>[
                'validationSubject'=>[
                    'response'=> []
                ],
                'isValid'=>true,
                'messages'=>[],
                'errorCodes'=>[]
            ]
        ];
    }
}
