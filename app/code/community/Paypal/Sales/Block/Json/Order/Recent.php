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

class Paypal_Sales_Block_Json_Order_Recent extends Mage_Sales_Block_Order_Recent
{
    public function _toHtml() {
        $orders = Mage::registry('current_order_list');
        $array = array();
        if (!is_null($orders)) {
            foreach($orders as $order) {
                $array[] = Mage::helper('paypal_sales')->getOrderDetails($order);
            }
        }
        return Mage::helper('core')->jsonEncode($array);
    }
}
