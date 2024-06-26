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

class ResponseValidator extends AbstractResponseValidator
{
    /**
     * @return array
     */
    protected function getResponseValidators():array
    {
        return [
            function ($validationSubject) {
                $response = $validationSubject['response'];

                $rule = !key_exists('errors', $response);
                $messageParts = [ __('ChargeAfter error') ];

                if (!$rule) {
                    if (key_exists('requestId', $response)) {
                        array_push($messageParts, 'Request ID: ' . $response['requestId']);
                    }

                    if (key_exists('errors', $response)) {
                        array_push($messageParts, 'Errors: ' . json_encode($response['errors']));
                    }
                }

                return [
                    $rule,
                    implode('. ', $messageParts)
                ];
            }
        ];
    }
}
