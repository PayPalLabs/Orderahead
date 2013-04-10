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

class Paypal_Catalog_Block_Json_Category_List extends Mage_Core_Block_Template
{
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $shortCacheId = array(
            'JSON_CATEGORY_LIST',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
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
	
	public function _toHtml(){
		try{
			$array = array();
			//$categoryHelper = Mage::helper('catalog/category');
			$categories = Mage::helper('paypal_catalog')->getStoreLevelOneCategories();//$categoryHelper->getStoreCategories(false, true, false);

			foreach ($categories as $category){
				//echo "<br/> ".$category->getName();
				$array["items"][] = Mage::helper('paypal_catalog')->getSubCategory($category);
			}
			return Mage::helper('core')->jsonEncode($array, JSON_FORCE_OBJECT);
		}catch(Exception $e){
			Mage::logException($e);
			return false;
		}
	}
	
	
	
}
