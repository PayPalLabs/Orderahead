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
class Paypal_Catalog_Block_Json_Product_List extends Mage_Core_Block_Template
{

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo() {
        $shortCacheId = array(
            'CATALOG_NAVIGATION',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getCurrenCategoryKey()
        );
        $cacheId = $shortCacheId;
        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['category_path'] = $this->getCurrenCategoryKey();
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }
	
	public function _toHtml() {
		try {
			$array = array();
            $category = Mage::registry('pp_current_category');
            if (!is_null($category)) {
    			$array = Mage::helper('paypal_catalog')->getProducts($category->getId());
                $this->setCurrenCategoryKey($category->getId());
            }
            else {
                $array = array('category' => '', 'items' => array());
            }
			return Mage::helper('core')->jsonEncode($array);
		} catch (Exception $e) {
			Mage::logException($e);
			return false;
		}
	}
	
	
	
}
