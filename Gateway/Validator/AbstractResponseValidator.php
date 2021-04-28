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
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

/**
 * Class AbstractResponseValidator
 * @package Chargeafter\Payment\Gateway\Validator
 */
abstract class AbstractResponseValidator extends AbstractValidator
{
    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $isValid = true;
        $errorMessages = [];
        $errorCodes = [];

        foreach ($this->getResponseValidators() as $validator) {
            $validationResult = $validator($validationSubject);

            if (!$validationResult[0]) {
                $isValid = $validationResult[0];
                $errorMessages = array_merge($errorMessages, $validationResult[1]);
            }
        }

        return $this->createResult($isValid, $errorMessages, $errorCodes);
    }

    /**
     * @return array
     */
    abstract protected function getResponseValidators(): array;

}
