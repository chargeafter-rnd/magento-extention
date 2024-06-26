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

namespace Chargeafter\Payment\Test\Unit\Block;

use Chargeafter\Payment\Block\PromotionalWidgetBlock;
use Magento\Checkout\Model\Session;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Registry\CurrentProductRegistry;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\TestCase;

class PromotionalWidgetBlockTest extends TestCase
{
    private $block;
    private $currentProductRegistry;
    private $helper;
    private $checkoutSession;
    private $layoutProcessor;

    protected function setUp(): void
    {
        $managerHelper = new ObjectManager($this);
        $this->helper = $this->createMock(ApiHelper::class);
        $this->currentProductRegistry = new CurrentProductRegistry();
        $this->checkoutSession = $this->createMock(Session::class);

        $this->layoutProcessor = $this->createMock(Merge::class);

        $layoutMock = $this->createMock(LayoutInterface::class);
        $layoutMock->expects($this->any())->method('getUpdate')->willReturn($this->layoutProcessor);

        $this->block = $managerHelper->getObject(PromotionalWidgetBlock::class, [
            'helper' => $this->helper,
            'currentProductRegistry' => $this->currentProductRegistry,
            'checkoutSession' => $this->checkoutSession,
            'layout' => $layoutMock
        ]);
    }

    public function testGetProduct()
    {
        $expected = $this->createMock(ProductInterface::class);
        $this->currentProductRegistry->set($expected);

        $actual = $this->block->getProduct();
        self::assertSame($expected, $actual);
    }

    public function testGetProductWithNull()
    {
        $expected = null;
        $actual = $this->block->getProduct();
        self::assertSame($expected, $actual);
    }

    public function testGetCart()
    {
        $this->layoutProcessor->method('getHandles')->willReturn([
            'checkout_cart_index'
        ]);

        $expected = $this->createMock(CartInterface::class);
        $this->checkoutSession->method('getQuote')->willReturn($expected);

        $actual = $this->block->getCart();

        self::assertSame($expected, $actual);
    }

    public function testGetCartWithNull()
    {
        $this->layoutProcessor->method('getHandles')->willReturn([
            'checkout_cart_index'
        ]);

        $expected = null;
        $actual = $this->block->getCart();
        self::assertSame($expected, $actual);
    }

    public function testGetCdnUrl()
    {
        $expected = 'https://cdn.test';
        $this->helper->expects($this->once())
            ->method('getCdnUrl')
            ->willReturn($expected);
        $actual = $this->block->getCdnUrl();
        self::assertSame($expected, $actual);
    }

    public function testGetPublicKey()
    {
        $expected = '1234567';
        $this->helper->expects($this->once())
            ->method('getPublicKey')
            ->willReturn($expected);
        $actual = $this->block->getPublicKey();
        self::assertSame($expected, $actual);
    }
}
