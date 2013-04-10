<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Pickup
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Renderer_Country extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $countryId = $element->getForm()->getElement('country')->getValue();
        $_countries = Mage::getResourceModel('directory/country_collection')
            ->loadData()
            ->toOptionArray(false);

        $html = '<tr>';
        $html .= '
            <td class="label">
                <label for="country">' . Mage::helper('paypal_pickup')->__('Country') . '<span class="required"> *</span></label>
            </td>
        ';
        $html .= '
            <td class="value">
                <div class="input-box">
                    <select name="country" id="country" class="required-entry countries select">
                        <option value="">' . Mage::helper('paypal_pickup')->__('-- Please Select --') . '</option>';
        foreach ($_countries as $_country) {
            $selected = ($_country['value'] == $countryId) ? 'selected="selected"' : "";
            $html .= '<option value="' . $_country['value'] . '" ' . $selected . '>' . $_country['label'] . '</option>';
        }
        $html .= '</select></div>';
        $html .= '</td></tr>';

        return $html;
    }
}

