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
class Paypal_Favorite_Helper_Data extends Mage_Core_Helper_Data
{
    protected $customerId;
    protected $customerType;

    public function initCustomer() {
        $this->customerId = Mage::registry('pp_fav_customer_id');
        $this->customerType = Mage::registry('pp_fav_customer_type');

        if (!is_null($this->customerId)) {
            return ;
        }

        $paypal_customer_id = Mage::getSingleton('core/session')->getCheckinCustomerId();
        if (isset($paypal_customer_id) && $paypal_customer_id != '') {
            $this->customerId = $paypal_customer_id;
            $this->customerType = 2;
        }
        else if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $this->customerType = 1;
        }
        else {
            $session = Mage::getSingleton('core/session', array('name' => 'frontend'));
            $this->customerId = $session->getSessionId();
            $this->customerType = 3;
        }
        Mage::register('pp_fav_customer_id', $this->customerId);
        Mage::register('pp_fav_customer_type', $this->customerType);
    }

    public function getCustomerId() {
        return $this->customerId;
    }

    public function getCustomerType() {
        return $this->customerType;
    }

    public function getProductStatus($product_id) {
        $this->initCustomer();
        if (is_null($this->customerId) || $this->customerType == 0) {
            return 0;
        }

        $favorites = Mage::getModel('paypal_favorite/customer_favorite')
                ->getCollection()
                ->addFieldToFilter('customer_id', $this->customerId)
                ->addFieldToFilter('customer_id_type', $this->customerType)
                ->addFieldToFilter('product_id', $product_id)
                ->getFirstItem();

        if (count($favorites->getData()) > 0)
            $result = 1;
        else $result = 0;

        return $result;
    }

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
