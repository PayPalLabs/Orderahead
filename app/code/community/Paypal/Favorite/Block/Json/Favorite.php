<?php
/**
 * PayPal Favorite
 *
 * @package      :  PayPal_Favorite
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
class Paypal_Favorite_Block_Json_Favorite extends Paypal_Favorite_Block_Json_Abstract
{
    public function _toHtml() {
        $products = Mage::registry('current_product_list');
        $array = array();

        if (is_null($products)) {
            return Mage::helper('core')->jsonEncode(array('items' => $array));
        }

        foreach($products as $product) {
            $img = Mage::helper('paypal_favorite')->getProductMedia($product->getId(), false);
            if ($img && count($img) > 0) {
                $img = reset($img);
                $img = $img['url'];
            }
            
            else
                $img = '';
            $details = Mage::helper('paypal_favorite')->getProductDetails($product->getId());

            $array[] = array(
                'id' => $product->getId(),
                'title' => $product->getName(),
                'image' => $img,
                'description' => $details['shortdesc'],
                'url' => Mage::app()->getStore()->getBaseUrl().'/jsoncatalog/product/viewtemplate/id/'.$product->getId(),
            );
        }
        return Mage::helper('core')->jsonEncode(array('items' => $array));
    }
}
