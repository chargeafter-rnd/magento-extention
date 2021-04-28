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

namespace Chargeafter\Payment\Test\Unit\Data\Form\Element;

use Chargeafter\Payment\Data\Form\Element\Radios;
use Magento\Framework\Data\Form;
use Magento\Framework\Escaper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Asset\Repository;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class RadiosTest extends TestCase
{
    /**
     * @param $option
     * @param $selected
     * @param $expected
     * @throws ReflectionException
     *
     * @dataProvider dataProviderTestGetHtml
     */
    public function testGetHtml($option, $selected, $expected)
    {
        $objectManager = new ObjectManager($this);
        $_assetRepo = $this->createMock(Repository::class);
        $_assetRepo->expects($option['value'] ? $this->once() : $this->never())
            ->method('getUrl')
            ->with("Chargeafter_Payment::images/" . $option['value'] . ".svg")
            ->willReturn("https://v236-charge-after.dev.lagan.com.ua/static/version1612575068/adminhtml/Magento/backend/en_US/Chargeafter_Payment/images/$option[value].svg");
        $radios = $objectManager->getObject(Radios::class, [
            '_assetRepo'=>$_assetRepo,
            'escaper'=>new Escaper(),
            'data'=>[
                'name'=>'groups[chargeafter][groups][chargeafter_base][fields][logo][value]',
                'html_id'=>'payment_us_chargeafter_chargeafter_base_logo'
            ]
        ]);
        $form = $this->createMock(Form::class);
        $radios->setForm($form);
        $class = new \ReflectionClass(Radios::class);

        $method = $class->getMethod('_optionToHtml');

        $method->setAccessible(true);

        $actual = $method->invokeArgs($radios, [$option, $selected]);

        self::assertSame($actual, $expected);
    }

    public function dataProviderTestGetHtml()
    {
        return[
            [
                'option'=>[
                    'value'=>'',
                    'label'=>'No Image'
                ],
                'selected'=>'Btn_CF',
                'expected'=>'<div class="admin__field admin__field-option"><input type="radio" type="radios"  name="groups[chargeafter][groups][chargeafter_base][fields][logo][value]" value="" class="admin__control-radio" id="payment_us_chargeafter_chargeafter_base_logo" /><label class="admin__field-label admin__field-label_with-image" for="payment_us_chargeafter_chargeafter_base_logo"><span>No Image</span></label></div>'
            ],
            [
                'option'=>[
                    'value' => 'Btn_CA',
                    'label' => 'Btn_CA',
                ],
                'selected'=>'Btn_CF',
                'expected'=>'<div class="admin__field admin__field-option"><input type="radio" type="radios"  name="groups[chargeafter][groups][chargeafter_base][fields][logo][value]" value="Btn_CA" class="admin__control-radio" id="payment_us_chargeafter_chargeafter_base_logoBtn_CA" /><label class="admin__field-label admin__field-label_with-image" for="payment_us_chargeafter_chargeafter_base_logoBtn_CA"><span><img type="hidden" src="https://v236-charge-after.dev.lagan.com.ua/static/version1612575068/adminhtml/Magento/backend/en_US/Chargeafter_Payment/images/Btn_CA.svg"></span></label></div>'
            ],
            [
                'option'=>[
                    'value' => 'Btn_CF',
                    'label' => 'Btn_CF',
                ],
                'selected'=>'Btn_CF',
                'expected'=>'<div class="admin__field admin__field-option"><input type="radio" type="radios"  name="groups[chargeafter][groups][chargeafter_base][fields][logo][value]" value="Btn_CF" class="admin__control-radio" id="payment_us_chargeafter_chargeafter_base_logoBtn_CF" checked="checked" /><label class="admin__field-label admin__field-label_with-image" for="payment_us_chargeafter_chargeafter_base_logoBtn_CF"><span><img type="hidden" src="https://v236-charge-after.dev.lagan.com.ua/static/version1612575068/adminhtml/Magento/backend/en_US/Chargeafter_Payment/images/Btn_CF.svg"></span></label></div>'
            ]
        ];
    }
}
