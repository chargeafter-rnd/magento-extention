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

namespace Chargeafter\Payment\Block;

use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Registry\CurrentProductRegistry;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Class PromotionalWidgetBlock
 * @package Chargeafter\Payment\ViewModel
 */
class PromotionalWidgetBlock extends Template implements BlockInterface
{
    protected $_template = "widget.phtml";
    /**
     * @var CurrentProductRegistry
     */
    private $currentProductRegistry;

    protected $_helper;

    /**
     * PromotionalWidgetBlock constructor.
     * @param ApiHelper $helper
     * @param CurrentProductRegistry $currentProductRegistry
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        ApiHelper $helper,
        CurrentProductRegistry $currentProductRegistry,
        Template\Context $context,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->currentProductRegistry = $currentProductRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface
    {
        return $this->currentProductRegistry->get();
    }

    /**
     * @return string
     */
    public function getCdnUrl(): string
    {
        return $this->_helper->getCdnUrl();
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->_helper->getPublicKey();
    }
}
