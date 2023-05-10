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

namespace Chargeafter\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Model\MethodInterface;
use Chargeafter\Payment\Helper\ApiHelper;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'chargeafter';

    /**
     * @var MethodInterface
     */
    protected $_method;

    /**
     * @var ApiHelper
     */
    protected $_helper;

    /**
     * ConfigProvider constructor.
     * @param MethodInterface $method
     * @param ApiHelper $helper
     */
    public function __construct(
        MethodInterface $method,
        ApiHelper $helper
    ) {
        $this->_method = $method;
        $this->_helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return [
            'payment'=>[
                self::CODE=>[
                    'description'=>$this->_method->getConfigData('description'),
                    'cdnUrl' => $this->_helper->getCdnUrl(),
                    'publicKey' => $this->_helper->getPublicKey()
                ]
            ]
        ];
    }
}
