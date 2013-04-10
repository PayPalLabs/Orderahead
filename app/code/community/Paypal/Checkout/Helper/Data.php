<?php
/**
 * PayPal Checkout
 *
 * @package      :  PayPal_Checkout
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
class Paypal_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getNumberOfItems(){
		$cart = Mage::helper('checkout/cart')->getQuote()->getData();
		if(isset($cart['items_qty'])){
			return (int)($cart['items_qty']);
		}
		else {
			return 0;
		}
	}
	
    public function getStaticBlock(){                
            $staticBlock = Mage::getModel('core/email_template_filter')->filter(
                Mage::getModel('cms/block')
                    ->load(Mage::getStoreConfig('checkout/paypal_cart/static_block'))
                    ->getContent()
            );
            if(is_null($staticBlock)){
                $staticBlock = '';
            }
            return $staticBlock;
    }
}
