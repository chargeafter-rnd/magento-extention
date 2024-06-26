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

use Chargeafter\Payment\Gateway\Validator\PostSaleResponseValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Validator\Result;

use PHPUnit\Framework\TestCase;

class PostSaleResponseValidatorTest extends TestCase
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
        $responseValidator = new PostSaleResponseValidator($resultFactory);
        $actual = $responseValidator->validate($validationSubject);
        self::assertEquals($result, $actual);
    }

    /**
     * @return array
     */
    public function dataProviderTestValidate(): array
    {
        return [
            'Invalid response with message' => [
                'validationSubject' => [
                    'response' => [
                        'code' => 123,
                        'message' => 'Error Code 123'
                    ]
                ],
                'isValid' => false,
                'messages' => [
                    'ChargeAfter error. Unable to execute post-sale operation for the charge: Error Code 123'
                ],
                'errorCodes' => []
            ],
            'Invalid response without message' => [
                'validationSubject' => [
                    'response' => [
                        'code' => 123,
                    ]
                ],
                'isValid' => false,
                'messages' => [
                    'ChargeAfter error. Unable to execute post-sale operation for the charge'
                ],
                'errorCodes' => []
            ],
            'Valid response' => [
                'validationSubject' => [
                    'response' => []
                ],
                'isValid' => true,
                'messages' => [],
                'errorCodes' => []
            ]
        ];
    }
}
