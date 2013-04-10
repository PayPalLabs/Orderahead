<?php
/**
 * Sales
 *
 * @package      :  Paypal_Sales
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

class Paypal_Sales_Block_Json_Order_Details extends Mage_Core_Block_Template
{
    public function _toHtml() {
        $order = Mage::registry('current_order');
        $result = array();

        if (is_null($order)) {
            return Mage::helper('core')->jsonEncode($result);
        }

        $result = Mage::helper('paypal_sales')->getOrderDetails($order);
        return Mage::helper('core')->jsonEncode($result);
    }
}