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
        $currentErrorMessages = [];

        foreach ($this->getResponseValidators() as $validator) {
            $validationResult = $validator($validationSubject);

            if (!$validationResult[0]) {
                $isValid = $validationResult[0];
                $currentErrorMessages[] = $validationResult[1];
            }
        }

        if (!empty($currentErrorMessages)) {
            $errorMessages = array_merge([], ...$currentErrorMessages);
        }

        return $this->createResult($isValid, $errorMessages, $errorCodes);
    }

    /**
     * @return array
     */
    abstract protected function getResponseValidators(): array;
}
