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

namespace Chargeafter\Payment\Data\Form\Element;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Class Radios
 * @package Chargeafter\Payment\Data\Form\Element
 */
class Radios extends \Magento\Framework\Data\Form\Element\Radios
{
    /**
     * @var AssetRepository
     */
    protected $_assetRepo;

    /**
     * Radios constructor.
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param AssetRepository $assetRepo
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        AssetRepository $assetRepo,
        $data = []
    ) {
        $this->_assetRepo = $assetRepo;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @param array $option
     * @param array $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        $html = '<div class="admin__field admin__field-option">' .
            '<input type="radio"' . $this->getRadioButtonAttributes($option);
        $html .= 'value="' . $this->_escape(
            $option['value']
        ) . '" class="admin__control-radio" id="' . $this->getHtmlId() . $option['value'] . '"';
        if ($option['value'] == $selected) {
            $html .= ' checked="checked"';
        }
        $html .= ' />';
        $html .= '<label class="admin__field-label admin__field-label_with-image" for="' .
            $this->getHtmlId() .
            $option['value'] .
            '">';
        $html .= '<span>';
        if ($option['value']) {
            $html .= '<img type="hidden" src="' .
            $this->_assetRepo->getUrl("Chargeafter_Payment::images/" . $option['value'] . ".svg") .
            '">';
        } else {
            $html .= $option['label'];
        }
        $html .= '</span>';
        $html .= '</label>';
        $html .= '</div>';
        return $html;
    }
}
