<?php
/**
 * Featuredcategory
 *
 * @package      :  Paypal_Featuredcategory
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

class Paypal_Featuredcategory_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getFeaturedCategories(){
		$array = array();

		$helper = Mage::helper('catalog/category');
		$categories = $helper->getStoreCategories();

        $storeId = Mage::app()->getStore()->getStoreId();

		foreach($categories as $category) {
            $category_id = $category->getId();
            $category = Mage::getModel('catalog/category')
                            ->setStoreId($storeId)
                            ->load($category_id);
            if ($category->getIsFeatured() && $category->getIsActive()) {
				$tempArray = array(
						"title" => $category->getName(),
						"description" => $category->getDescription(),
						"image" => $category->getImageUrl()?$category->getImageUrl():Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg'),
						"id" => $category_id,
						"url" => Mage::app()->getStore()->getBaseUrl().'/jsoncatalog/product/listtemplate/id/'.$category->getId(),
				);
				$array["items"][] = $tempArray;
            }
		}
		return $array;
	}
	
}
