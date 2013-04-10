<?php
/**
 * PayPal Checkin
 *
 * @package      :  PayPal_Checkin
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 */
class Paypal_Checkin_Block_Adminhtml_Authlink extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {  
        $html = $this->getLayout()->createBlock('paypal_checkin/adminhtml_linkcontent')
                    ->toHtml();

        return $html;
    }
}
