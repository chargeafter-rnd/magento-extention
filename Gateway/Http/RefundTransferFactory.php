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

namespace Chargeafter\Payment\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferInterface;

/**
 * Class RefundTransferFactory
 * @package Chargeafter\Payment\Gateway\Http
 */
class RefundTransferFactory extends TransferFactory
{
    /**
     * @inheritDoc
     */
    public function create(array $request): TransferInterface
    {
        return $this->_transferBuilder
            ->setUri($this->_apiHelper->getApiUrl("/post-sale/charges/$request[chargeId]/refunds", $request['storeId']))
            ->setMethod('POST')
            ->setHeaders([
                'Authorization'=>'Bearer ' . $this->_apiHelper->getPrivateKey($request['storeId'])
            ])
            ->setBody($request['payload'])
            ->build();
    }
}
