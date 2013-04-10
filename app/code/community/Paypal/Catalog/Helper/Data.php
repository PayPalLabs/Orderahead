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
class Paypal_Catalog_Helper_Data extends Mage_Catalog_Helper_Category {

    /**
     * Store categories cache
     *
     * @var array
     */
    protected $_storeCategories = array();

    public function getSubCategory($category) {
        $array = array();
        $tempArray = array();
        $category_id = $category->getId();
        $tempArray['id'] = $category_id;
        $tempArray['name'] = $category->getName();
        $tempArray['urlkey'] = $category->getUrlKey();
        /*
         * get current category
         */
        $current_category = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->load($category_id);
        $tempArray['img'] = $current_category->getImageUrl()?$current_category->getImageUrl():Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg');
        $tempArray['desc'] = $current_category->getDescription();

        if ($category->hasChildren()) {
            $children = array();
            foreach ($category->getChildrenCategories() as $childCategory) {
                $children[] = $this->getSubCategory($childCategory, $children);
            }
            $tempArray["items"] = $children;
            $array = $tempArray;
        } else {
            $array = $tempArray;
        }

        return $array;
    }

    public function getStoreLevelOneCategories() {
        $cacheKey = sprintf('%d-%d-%d', Mage::app()->getStore()->getRootCategoryId(), false, true);
        //if found in cache return it
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }
        //gjeyapaul: TODO : check for is_anchor
        $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addFieldToFilter('parent_id', array('eq' => Mage::app()->getStore()->getRootCategoryId()))
                ->addFieldToFilter('is_active', array('eq' => '1'))
                ->addAttributeToSelect('image_url');
        $this->_storeCategories[$cacheKey] = $categories;
        return $categories;
    }

    //TODO : add some kind of caching as the above method
    public function getProducts($categoryId) {
        $array = array();
        $category = Mage::registry('pp_current_category');
        if (is_null($category) && !isset($categoryId)) {
            if (!isset($categoryId)) return ;
            else {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                if ($category->getId() !== $categoryId) {
                    return ;
                }
            }
            return;
        }
        $array['category'] = $category->getName();
        $collection = $category->getProductCollection()
        				->addAttributeToSelect("*")
        				->addAttributeToFilter('visibility' , Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        foreach ($collection as $product) {
            $tempArray = array(
                "title" => $product->getName(),
                "description" => $product->getShortDescription(),
                "image" => (string) Mage::helper('catalog/image')->init($product, 'small_image'),
                "id" => $product->getId(),
                "url" => Mage::app()->getStore()->getBaseUrl() . '/jsoncatalog/product/viewtemplate/id/' . $product->getId(),
                "price" => number_format($product->getPrice(), 2, '.', '')
            );
            $array["items"][] = $tempArray;
        }
        return $array;
    }

    //TODO : add some kind of caching as the above method
    public function getProductMedia($productId, $registerProduct = true) {
        $array = array();
        if (!isset($productId)) {
            return;
        }
        $product = Mage::registry('pp_current_product');
        if (!$product) {
            $product = Mage::getModel("catalog/product")->load($productId);
        }
        
        $collection = $product->getMediaGalleryImages();
        Mage::log("Size : " . $collection->getSize());
        foreach ($collection as $item) {
            Mage::log(var_export($item, true));
            $tempArray = array(
                "id" => $item->getId(),
                "url" => $item->getUrl(),
                "path" => $item->getPath()
            );
            $array[] = $tempArray;
        }
        if (empty($array)) {
            $array[] = array(
                "id" => 1,
                "url" => (string) Mage::helper('catalog/image')->init($product, 'image'),
                "path" => ""
            );
        }
        return $array;
    }

    public function getProductDetails($productId) {
        $product = Mage::registry("pp_current_product");
        if (!isset($product)) {
            $product = Mage::getModel("catalog/product")->load($productId);
        }
        $tempArray = array(
            "id" => $product->getId(),
            "name" => $product->getName(),
            "price" => number_format($product->getPrice(), 2, '.', ''),
            "specialprice" => number_format($product->getSpecialPrice(), 2, '.', ''),
            "finalprice" => number_format($product->getFinalPrice(), 2, '.', ''),
            "url" => $product->getProductUrl(),
            "shortdesc" => $product->getShortDescription(),
            "longdesc" => $product->getDescription(),
            "availability" => $product->isSaleable() ? "In stock" : "Out of Stock",
            "minqty" => $product->getStockItem()->getMinSaleQty(),
            "maxqty" => $product->getStockItem()->getMaxSaleQty(),
            "currency_code" => Mage::app()->getStore()->getCurrentCurrencyCode(),
            "options" => $this->productOptions($product),
        );
        return $tempArray;
    }

    public function productOptions($product) {
        $eavAttributeCollection = Mage::getSingleton('eav/entity_attribute')->getCollection();
        $optionArray = array();
        $suboptions = array();
        $options = array();
        try {
            $productOptions = $product->getProductOptionsCollection();
            foreach ($productOptions as $option) {
                $array = array();
                $array['option_id'] = $option->getId();
                $array['configurable'] = 0;
                $array['title'] = $option->getTitle();
                $array['type'] = $option->getType();
                if ($array['type'] !== 'drop_down' && $array['type'] !== 'radio' && $array['type'] !== 'checkbox') {
                    $array['price'] = number_format($option->getPrice(), 2, '.', ''); //Mage::helper('paypal_sales')->simpleFormatPrice($option->getPrice());
                }
                $suboptions = array();
                foreach ($option->getValues() as $key => $suboption) {
                    $subarray = array();
                    $subarray['option_id'] = $suboption->getId();
                    $subarray['title'] = $suboption->getTitle();
                    $subarray['price'] = number_format($suboption->getPrice(), 2, '.', ''); //Mage::helper('paypal_sales')->simpleFormatPrice($suboption->getPrice());
                    $subarray['sku'] = $suboption->getSku();
                    $subarray['sort_order'] = $suboption->getSortOrder();
                    $suboptions[] = $subarray;
                }
                $array['options'] = $suboptions;
                $array['is_require'] = $option->getIsRequire();
                $array['sort_order'] = $option->getSortOrder();
                $optionArray[] = $array;
            }
            if ($product->getTypeId() == 'configurable') {
                //load all used configurable attributes
                $configurableAttributeCollection = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                $array = array();
                foreach ($configurableAttributeCollection as $attribute) {
                    $array = array();
                    $array['option_id'] = $attribute['attribute_id'];
                    $array['title'] = $attribute['label'];
                    $array['type'] = 'drop_down';
                    $array['configurable'] = 1;
                    $suboptions = array();
                    foreach ($attribute['values'] as $subattribute) {
                        $subarray = array();
                        $subarray['option_id'] = $subattribute['value_index'];
                        $subarray['title'] = $subattribute['label'];
                        $subarray['price'] = number_format((int) $subattribute['pricing_value'], 2, '.', '');
                        $subarray['sku'] = '';
                        $subarray['sort_order'] = $subattribute['value_index'];
                        $suboptions[] = $subarray;
                    }
                    $array['options'] = $suboptions;
                    $array['is_require'] = $eavAttributeCollection
                                            ->addFieldToFilter('attribute_id', $attribute['attribute_id'])
                                            ->getFirstItem()
                                            ->getIsRequired();
                    $array['sort_order'] = $attribute['id'];
                    $optionArray[] = $array;
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $optionArray;
    }

}