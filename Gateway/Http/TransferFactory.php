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

use Chargeafter\Payment\Helper\ApiHelper;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;

/**
 * Class TransferFactory
 * @package Chargeafter\Payment\Gateway\Http
 */
abstract class TransferFactory implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    protected $_transferBuilder;
    /**
     * @var ApiHelper
     */
    protected $_apiHelper;

    /**
     * @param TransferBuilder $transferBuilder
     * @param ApiHelper $apiHelper
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        ApiHelper $apiHelper
    ) {
        $this->_transferBuilder=$transferBuilder;
        $this->_apiHelper = $apiHelper;
    }
}
