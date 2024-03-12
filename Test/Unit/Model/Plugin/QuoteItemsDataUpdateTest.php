<?php

namespace Chargeafter\Payment\Test\Unit\Model\Plugin;

use Chargeafter\Payment\Model\Plugin\QuoteItemsDataUpdate;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Model\Quote;
use PHPUnit\Framework\TestCase;

class QuoteItemsDataUpdateTest extends TestCase
{
    private $checkoutSessionMock;

    private $cartItemRepositoryMock;

    private $productRepositoryMock;

    private $subjectMock;

    private $plugin;

    protected function setUp(): void
    {
        $this->checkoutSessionMock = $this->createMock(Session::class);
        $this->cartItemRepositoryMock = $this->createMock(CartItemRepositoryInterface::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->subjectMock = $this->createMock(DefaultConfigProvider::class);

        $this->plugin = new QuoteItemsDataUpdate(
            $this->checkoutSessionMock,
            $this->cartItemRepositoryMock,
            $this->productRepositoryMock
        );
    }

    /**
     * @param $data
     * @param $expected
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider configDataProvider
     */
    public function testAfterConfig($data, $expected)
    {
        $quoteMock = $this->createMock(Quote::class);
        $quoteMock->expects($this->once())->method('getId')->willReturn(32);

        $this->checkoutSessionMock->expects($this->once())->method('getQuote')->willReturn($quoteMock);

        $quoteItems = [
            $this->createQuoteItemMock($data, 0),
            $this->createQuoteItemMock($data, 1),
            $this->createQuoteItemMock($data, 2),
        ];

        $this->productRepositoryMock->expects($this->exactly(3))
             ->method('getById')
             ->willReturnCallback(function ($id) use ($quoteItems) {
                 return $quoteItems[$id]->getProduct();
             });

        $this->cartItemRepositoryMock->expects($this->once())->method('getList')->willReturn($quoteItems);

        $result = $this->plugin->afterGetConfig($this->subjectMock, ['quoteItemData' => $data]);

        $this->assertEquals($expected, $result);
    }

    private function createQuoteItemMock($data, $index)
    {
        $data = $data[$index];

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())->method('getId')->willReturn($index);
        $productMock->expects($this->exactly(2))->method('getDataByKey')
            ->will($this->returnCallback(
                function ($key) use ($data) {
                    return $data['attribute'][$key];
                })
            );

        $quoteItemMock = $this->createMock(Quote\Item::class);
        $quoteItemMock->expects($this->any())->method('getProduct')->willReturn($productMock);
        $quoteItemMock->expects($this->any())->method('getItemId')->willReturn($data['item_id']);

        return $quoteItemMock;
    }

    /**
     * @return array
     */
    public function configDataProvider()
    {
        return [
            [
                'data' => [
                    [
                        'item_id' => 51,
                        'attribute' => [
                            'chargeafter_non_leasable' => '0',
                            'chargeafter_warranty' => '0'
                        ]
                    ],
                    [
                        'item_id' => 52,
                        'attribute' => [
                            'chargeafter_non_leasable' => '1',
                            'chargeafter_warranty' => '1'
                        ]
                    ],
                    [
                        'item_id' => 53,
                        'attribute' => [
                            'chargeafter_non_leasable' => null,
                            'chargeafter_warranty' => null
                        ]
                    ],
                ],
                'expected' => [
                    'quoteItemData' => [
                        [
                            'item_id' => 51,
                            'attribute' => [
                                'chargeafter_non_leasable' => '0',
                                'chargeafter_warranty' => '0'
                            ],
                            'ca_is_leasable' => true,
                            'ca_with_warranty' => false
                        ],
                        [
                            'item_id' => 52,
                            'attribute' => [
                                'chargeafter_non_leasable' => '1',
                                'chargeafter_warranty' => '1'
                            ],
                            'ca_is_leasable' => false,
                            'ca_with_warranty' => true
                        ],
                        [
                            'item_id' => 53,
                            'attribute' => [
                                'chargeafter_non_leasable' => null,
                                'chargeafter_warranty' => null
                            ],
                            'ca_is_leasable' => true,
                            'ca_with_warranty' => false
                        ],
                    ]
                ]
            ]
        ];
    }
}
