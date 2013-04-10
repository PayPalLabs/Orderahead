<?php
/**
 * PayPal Catalog 
 *
 * @package      :  PayPal_Catalog
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
class Paypal_Catalog_Block_Json_Product_View_Media extends Mage_Catalog_Block_Product_View_Media
{

    public function _construct() {
        $product = Mage::registry('pp_current_product');
        if ($product) {
            $this->addData(array(
                'cache_lifetime'    => false,
                'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG . "_media_" . $product->getId(),
                                            Mage::app()->getStore()->getId(),
                                            Mage::getDesign()->getPackageName(),
                                            Mage::getDesign()->getTheme('template'),
                                            Mage::getSingleton('customer/session')->getCustomerGroupId()
                                        ),
                'cache_key'         => 'media_' . $product->getId().'--'.Mage::app()->getStore()->getId().'---'.
                                            Mage::getDesign()->getPackageName().'-'.
                                            Mage::getDesign()->getTheme('template').'----'.
                                            Mage::getSingleton('customer/session')->getCustomerGroupId()
            ));
        }
    }

    public function _toHtml() {
        try {
            $array = array();
            $product = Mage::registry('pp_current_product');
            $array = Mage::helper('paypal_catalog')->getProductMedia($product->getId());
            return Mage::helper('core')->jsonEncode($array);
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

}
