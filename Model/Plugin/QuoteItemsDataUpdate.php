<?php

namespace Chargeafter\Payment\Model\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class QuoteItemsDataUpdate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var QuoteItemRepository
     */
    private $quoteItemRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * QuoteItemsDataUpdate Construct
     *
     * @param CheckoutSession $checkoutSession
     * @param QuoteItemRepository $quoteItemRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        QuoteItemRepository $quoteItemRepository,
        ProductRepository $productRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Execute method after config getting
     *
     * @param DefaultConfigProvider $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        $result['quoteItemData'] = $this->getQuoteItemData();

        return $result;
    }

    /**
     * Retrieve quote item data
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getQuoteItemData()
    {
        $quoteItemData = [];
        $quoteId = $this->checkoutSession->getQuote()->getId();

        if ($quoteId) {
            $quoteItems = $this->quoteItemRepository->getList($quoteId);

            foreach ($quoteItems as $index => $quoteItem) {
                $quoteItemData[$index] = $quoteItem->toArray();
                $quoteItemData[$index]['chargeafter'] = $this->getProductOptions($quoteItem);
            }
        }

        return $quoteItemData;
    }

    /**
     * Get product options
     *
     * @param $quoteItem
     * @return array
     */
    private function getProductOptions($quoteItem)
    {
        $options = [ 'leasable' => true, 'warranty' => false ];

        try {
            $productId = $quoteItem->getProduct()->getIdBySku($quoteItem->getSku());
            if ($productId == null) {
                $productId = $quoteItem->getProduct()->getId();
            }
            $product = $this->productRepository->getById($productId);

            $isNonLeasable = $product->getDataByKey('chargeafter_non_leasable');
            $isWarranty = $product->getDataByKey('chargeafter_warranty');

            if (!is_null($isNonLeasable)) {
                $options['leasable'] = (bool)$isNonLeasable === false;
            }

            if (!is_null($isWarranty)) {
                $options['warranty'] = (bool)$isWarranty;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            //
        }

        return $options;
    }
}
