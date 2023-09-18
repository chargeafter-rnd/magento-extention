<?php

namespace Chargeafter\Payment\Model\Config\Source;

use Chargeafter\Payment\Api\TransactionTypeInterface;

class TransactionType implements \Magento\Framework\Data\OptionSourceInterface, TransactionTypeInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TRANSACTION_TYPE_AUTHORIZATION,
                'label' => 'Authorization',
            ],
            [
                'value' => self::TRANSACTION_TYPE_CAPTURE,
                'label' => 'Capture'
            ]
        ];
    }
}
