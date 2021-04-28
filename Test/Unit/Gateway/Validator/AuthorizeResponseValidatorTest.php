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

use Chargeafter\Payment\Gateway\Validator\AuthorizeResponseValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\Result;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class AuthorizeResponseValidatorTest extends TestCase
{
    /**
     * @param array $validationSubject
     * @param bool $isValid
     * @param array $messages
     * @param array $errorCodes
     * @return void
     * @throws ReflectionException
     *
     * @dataProvider dataProviderTestValidate
     *
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
        $responseValidator = new AuthorizeResponseValidator($resultFactory);
        $actual = $responseValidator->validate($validationSubject);
        self::assertEquals($result, $actual);
    }

    /**
     * @return array
     */
    public function dataProviderTestValidate(): array
    {
        return [
            [
                'validationSubject'=>[
                    'response'=>[
                        'state'=>'AUTHORIZED',
                    ]
                ],
                'isValid'=>true,
                'messages'=>[],
                'errorCodes'=>[]
            ],
            [
                'validationSubject'=>[
                    'response'=>[
                        'state'=>'DECLINED',
                        'message'=>'Transaction was declined.'
                    ]
                ],
                'isValid'=>false,
                'messages'=>[
                    'Transaction was declined.'
                ],
                'errorCodes'=>[]
            ]
        ];
    }
}
