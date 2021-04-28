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
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Model\MethodInterface;
use Chargeafter\Payment\Helper\ApiHelper;

/**
 * Class ConfigProvider
 * @package Chargeafter\Payment\Model\Ui
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'chargeafter';
    /**
     * @var MethodInterface
     */
    protected $_method;
    /**
     * @var AssetRepository
     */
    protected $_assetRepo;
    /**
     * @var ApiHelper
     */
    protected $_helper;

    /**
     * ConfigProvider constructor.
     * @param MethodInterface $method
     * @param AssetRepository $assetRepo
     * @param ApiHelper $helper
     */
    public function __construct(
        MethodInterface $method,
        AssetRepository $assetRepo,
        ApiHelper $helper
    ) {
        $this->_method = $method;
        $this->_assetRepo = $assetRepo;
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
                    'logo'=> ($logo = $this->_method->getConfigData('logo')) ? $this->_assetRepo->getUrl("Chargeafter_Payment::images/" . $logo . ".svg") : null,
                    'cdnUrl' => $this->_helper->getCdnUrl(),
                    'publicKey' => $this->_helper->getPublicKey()
                ]
            ]
        ];
    }
}
