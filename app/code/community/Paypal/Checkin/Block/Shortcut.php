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
class Paypal_Checkin_Block_Shortcut extends Mage_Core_Block_Template {

    public function getAddToCartUrl() {
        return Mage::getBaseUrl() . "paypal_checkin/index/addToCart";
    }

    public function getInitCheckinUrl() {
        return Mage::getBaseUrl() . "paypal_checkin/index/initcheckin";
    }

    public function getCheckoutUrl() {
        return Mage::getBaseUrl() . "paypal_checkin/index/checkout";
    }

}