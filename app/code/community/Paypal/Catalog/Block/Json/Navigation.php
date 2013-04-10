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
class Paypal_Catalog_Block_Json_Navigation extends Mage_Catalog_Block_Navigation
{

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $shortCacheId = array(
            'CATALOG_JSON_NAVIGATION',
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

    /**
     * Render category to array
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return array
     */
    protected function _renderCategoryMenuItemArray($category, $level = 0, $isLast = false, $isFirst = false,
        $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $result_array = array();

        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // Available properties
        // $level
        // $this->_getItemPosition($level)
        // $outermostItemClass
        // $isFirst
        // $isLast
        // $hasActiveChildren
        // $this->getCategoryUrl($category)
        // $this->escapeHtml($category->getName())
        $category_id = $category->getId();
        $result_array['name'] = $this->escapeHtml($category->getName());
        $result_array['id'] = $this->escapeHtml($category_id);
        $current_category = Mage::getModel('catalog/category')
                                        ->setStoreId(Mage::app()->getStore()->getStoreId())
                                        ->load($category_id);
        $result_array['image'] = $current_category->getImageUrl()?$current_category->getImageUrl():'';
        $result_array['desc'] = $current_category->getDescription();
        
        if($hasActiveChildren && !$category->getIsAnchor()){

	        // render children
	        $j = 0;
	        foreach ($activeChildren as $child) {
	            $result_array['items'][] = $this->_renderCategoryMenuItemArray(
	                $child,
	                ($level + 1),
	                ($j == $activeChildrenCount - 1),
	                ($j == 0),
	                false
	            );
	            $j++;
	        }
        }

        return $result_array;
    }
    
    /**
     * Render categories menu in ARRAY
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return array
     */
    public function renderCategoriesMenuArray($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $result_array['url'] = Mage::app()->getStore()->getBaseUrl().'jsoncatalog/product/listtemplate';
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $result_array['items'] = array();
        $j = 0;
        foreach ($activeCategories as $category) {
            $result_array['items'][] = $this->_renderCategoryMenuItemArray(
									                $category,
									                $level,
									                ($j == $activeCategoriesCount - 1),
									                ($j == 0),
									                true);
            $j++;
        }

        return $result_array;
    }
    
    public function _toHtml(){
    	return Mage::helper('core')->jsonEncode($this->renderCategoriesMenuArray());
    }

}
