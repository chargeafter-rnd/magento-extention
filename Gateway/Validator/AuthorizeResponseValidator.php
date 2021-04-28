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

namespace Chargeafter\Payment\Gateway\Validator;
/**
 * Class AuthorizeResponseValidator
 * @package Chargeafter\Payment\Gateway\Validator
 */
class AuthorizeResponseValidator extends ResponseValidator
{
    /**
     * @return array
     */
    protected function getResponseValidators(): array
    {
        return [
            function ($validationSubject) {
                $response = $validationSubject['response'];
                return [
                    key_exists('state', $response) && $response['state'] === 'AUTHORIZED',
                    [$response['message'] ?? __('Braintree error response.')]
                ];
            }
        ];
    }
}
