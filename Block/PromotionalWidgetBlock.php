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
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\JsonHexTag;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Widget\Block\BlockInterface;

class PromotionalWidgetBlock extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget.phtml";

    /**
     * @var CurrentProductRegistry
     */
    private $currentProductRegistry;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var ApiHelper
     */
    protected $_helper;

    /**
     * JsonHexTag Serializer Instance
     *
     * @var JsonHexTag
     */
    private $serializer;

    /**
     * PromotionalWidgetBlock constructor.
     * @param ApiHelper $helper
     * @param CurrentProductRegistry $currentProductRegistry
     * @param CheckoutSession $checkoutSession
     * @param Context $context
     * @param JsonHexTag $json
     * @param array $data
     */
    public function __construct(
        ApiHelper $helper,
        CurrentProductRegistry $currentProductRegistry,
        CheckoutSession $checkoutSession,
        Template\Context $context,
        JsonHexTag $json,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->currentProductRegistry = $currentProductRegistry;
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $json;

        parent::__construct($context, $data);
    }

    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface
    {
        return $this->currentProductRegistry->get();
    }

    public function getCart(): ?CartInterface
    {
        try {
            $handle = $this->getLayout()->getUpdate()->getHandles();
            if (in_array('checkout_cart_index', $handle) && $this->checkoutSession) {
                return $this->checkoutSession->getQuote();
            }
        } catch (LocalizedException $e) {}

        return null;
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

    /**
     * @return string|null
     */
    public function getStoreId(): ?string
    {
        return $this->_helper->getStoreId();
    }

    /**
     * Get configuration for UI component
     *
     * @return string
     */
    public function getComponentJsonConfig(): string
    {
        return $this->serializer->serialize([
            'cdnUrl' => $this->getCdnUrl(),
            'caConfig' => [
                'apiKey' => $this->getPublicKey(),
                'storeId' => $this->getStoreId()
            ]
        ]);
    }
}
