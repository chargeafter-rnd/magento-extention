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
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Chargeafter\Payment\Helper\ApiHelper;
use Chargeafter\Payment\Registry\CurrentProductRegistry;
use Magento\Catalog\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;

class PromotionalWidgetBlockTest extends TestCase
{
    private $block;
    private $currentProductRegistry;
    private $helper;

    protected function setUp()
    {
        $managerHelper = new ObjectManager($this);
        $this->helper = $this->createMock(ApiHelper::class);
        $this->currentProductRegistry = new CurrentProductRegistry();
        $this->block = $managerHelper->getObject(PromotionalWidgetBlock::class, [
            'helper'=>$this->helper,
            'currentProductRegistry'=>$this->currentProductRegistry,
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
