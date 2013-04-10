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
class Paypal_Favorite_Block_Json_Productstatus extends Paypal_Favorite_Block_Json_Abstract
{
    public function _toHtml() {
        $result = array(
            'enable' => Mage::getStoreConfig('catalog/favorite/enable'),
            'status' => '0'
        );
        $this->initCustomer();
        if (is_null($this->customerId) || $this->customerType == 0) {
            return Mage::helper('core')->jsonEncode($result);
        }

        $product = Mage::registry('pp_current_product');

        if (is_null($product)) {
            return Mage::helper('core')->jsonEncode($result);
        }

        $favorites = Mage::getModel('paypal_favorite/customer_favorite')
                ->getCollection()
                ->addFieldToFilter('customer_id', $this->customerId)
                ->addFieldToFilter('customer_id_type', $this->customerType)
                ->addFieldToFilter('product_id', $product->getId())
                ->getFirstItem();

        if (count($favorites->getData()) > 0)
            $result['status'] = '1';

        return Mage::helper('core')->jsonEncode($result);
    }
}
