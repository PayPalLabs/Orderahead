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
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Renderer_Region extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $model = Mage::registry('place_data');
        $region = $model->getData('region');
        $regionId = $element->getForm()->getElement('region_id')->getValue();

        $html = '<tr>';
        $html .= '
            <td class="label">
                <label for="region_id">' . Mage::helper('paypal_pickup')->__('State/Province') . '</label>
            </td>
        ';
        $html .= '
            <td class="value">
                <div class="input-box">
                    <select id="region_id" name="region_id" title="' . Mage::helper('paypal_pickup')->__('State/Province') .'" class="regions" style="display:none;">
                        <option value="">' . Mage::helper('paypal_pickup')->__('Please select region, state or province') . '</option>
                    </select>
                    <script type="text/javascript">
                    //<![CDATA[
                        $(\'region_id\').setAttribute(\'defaultValue\', "' .$regionId . '");
                    //]]>
                    </script>
                    <input type="text" id="region" name="region" value="' . $region . '" title="' . Mage::helper('paypal_pickup')->__('State/Province') . '" class="input-text" style="display:none;" />
                </div>
            </td>
        ';
        $html .='
            <script type="text/javascript">
            //<![CDATA[
                new RegionUpdater(\'country\', \'region\', \'region_id\', ' . $this->helper('directory')->getRegionJson() . ');
            //]]>
            </script>
        ';
        $html .= '</tr>';

        return $html;
    }
}

