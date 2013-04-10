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
class Paypal_Catalog_Block_Json_Product_View extends Mage_Catalog_Block_Product_View
{
    public function _construct() {
        $product = Mage::registry('pp_current_product');
        if ($product) {
            $this->addData(array(
                'cache_lifetime'    => false,
                'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG . "_view_" . $product->getId(),
                                            Mage::app()->getStore()->getId(),
                                            Mage::getDesign()->getPackageName(),
                                            Mage::getDesign()->getTheme('template'),
                                            Mage::getSingleton('customer/session')->getCustomerGroupId()
                                        ),
                'cache_key'         => 'view_' . $product->getId().'--'.Mage::app()->getStore()->getId().'---'.
                                            Mage::getDesign()->getPackageName().'-'.
                                            Mage::getDesign()->getTheme('template').'----'.
                                            Mage::getSingleton('customer/session')->getCustomerGroupId()
            ));
        }
    }
    
    public function _toHtml(){
        $product = Mage::registry('pp_current_product');
        $array = array('media' => array(), 'details' => array());

        if ($product) {
            $array['media'] = Mage::helper('core')->jsonDecode($this->getChildHtml("media"));
            $array['details'] = Mage::helper('core')->jsonDecode($this->getChildHtml("details"));
        }
        return Mage::helper('core')->jsonEncode($array);
    }
}
