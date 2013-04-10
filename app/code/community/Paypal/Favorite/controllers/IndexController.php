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
class Paypal_Favorite_IndexController extends Paypal_Core_Controller_Front_Action
{
    protected function _initFavoriteList() {
        if (!Mage::getStoreConfig('catalog/favorite/enable')) {
            Mage::throwException(Mage::helper('paypal_favorite')->__('Favorite is not enabled'));
        }
        Mage::helper('paypal_favorite')->initCustomer();
        $customerId = Mage::registry('pp_fav_customer_id');
        $customerType = Mage::registry('pp_fav_customer_type');
        if (is_null($customerId)) {
            Mage::throwException(Mage::helper('paypal_favorite')->__('Invalid user'));
        }

        $favorites = Mage::getModel('paypal_favorite/customer_favorite')
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('customer_id_type', $customerType);

        $product_ids = array();
        foreach($favorites as $fav) {
            $product_ids[] = $fav['product_id'];
        }

        if (count($product_ids)) {
            $products = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getStoreId())
                    ->addFieldToFilter('entity_id', $product_ids)
                    ->addAttributeToSelect('id')
                    ->addAttributeToSelect('name');
        }
        else {
            $products = array();
        }
        Mage::register('current_product_list', $products);
    }

    public function viewAction() {
        $this->loadLayout();
        try {
            $this->_initFavoriteList();
            $this->renderJsonLayout();
        }
        catch (Mage_Core_Exception $ex) {
            $messages = array_unique(explode("\n", $ex->getMessage()));
            foreach($messages as $message) {
                Mage::getSingleton('core/session')->addError(Mage::helper('core')->escapeHtml($message));
            }
            $this->renderJsonLayout();
        }
        catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    public function viewtemplateAction() {
        $this->loadLayout();
        try {
            $this->_initFavoriteList();
            $this->renderLayout();
        }
        catch (Mage_Core_Exception $ex) {
            $messages = array_unique(explode("\n", $ex->getMessage()));
            foreach($messages as $message) {
                Mage::getSingleton('core/session')->addError(Mage::helper('core')->escapeHtml($message));
            }
            $this->renderLayout();
        }
        catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    public function addAction() {
        $this->loadLayout();
        Mage::helper('paypal_favorite')->initCustomer();
        $params = $this->getRequest()->getParams();

        $customerId = Mage::helper('paypal_favorite')->getCustomerId();
        $customerType = Mage::helper('paypal_favorite')->getCustomerType();

        try {
            if (!Mage::getStoreConfig('catalog/favorite/enable')) {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Favorite is not enabled'));
            }
            if (is_null($customerId) || $customerType == 0) {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Invalid user'));
            }
            if (!isset($params['product_id']) || $params['product_id'] == '') {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Invalid product ID'));
            }
            $product_id = $params['product_id'];
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($product_id);
            if (is_null($product) || $product->getId() != $product_id) {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Product does not exist'));
            }

            // Check if item is already in favorite
            $favorite = Mage::getModel('paypal_favorite/customer_favorite')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $product_id)
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('customer_id_type', $customerType)
                    ->getFirstItem();
            if (count($favorite->getData()) > 0) {
            }
            else {
                $favorite = Mage::getModel('paypal_favorite/customer_favorite');
                $favorite->setCustomerId($customerId);
                $favorite->setCustomerIdType($customerType);
                $favorite->setProductId($product_id);

                $favorite->save();
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('Product added to favorite'));
            Mage::register('favorite_add_success', 1);
        }
        catch (Mage_Core_Exception $ex) {
            $messages = array_unique(explode("\n", $ex->getMessage()));
            foreach($messages as $message) {
                Mage::getSingleton('core/session')->addError(Mage::helper('core')->escapeHtml($message));
            }
            Mage::register('favorite_add_success', 0);
        }
        catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError($this->__('An error has occurred. Please try again later'));
            Mage::register('favorite_add_success', 0);
        }
        $this->renderJsonLayout();
    }

    public function deleteAction() {
        $this->loadLayout();
        Mage::helper('paypal_favorite')->initCustomer();
        $params = $this->getRequest()->getParams();

        $customerId = Mage::helper('paypal_favorite')->getCustomerId();
        $customerType = Mage::helper('paypal_favorite')->getCustomerType();
        
        try {
            if (!Mage::getStoreConfig('catalog/favorite/enable')) {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Favorite is not enabled'));
            }
            if (is_null($customerId) || $customerType == 0) {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Invalid user'));
            }
            if (!isset($params['product_id']) || $params['product_id'] == '') {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Invalid product ID'));
            }
            $product_id = $params['product_id'];
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($product_id);
            if (is_null($product) || $product->getId() != $product_id) {
                Mage::throwException(Mage::helper('paypal_favorite')->__('Product does not exist'));
            }

            $favorite = Mage::getModel('paypal_favorite/customer_favorite')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $product_id)
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('customer_id_type', $customerType)
                    ->getFirstItem();
            // Only remove when item is in favorite.
            if (count($favorite->getData()) > 0) {
                $favorite->delete();
            }
            Mage::getSingleton('core/session')->addSuccess($this->__('Item successfully removed from favorites'));
            Mage::register('favorite_delete_success', 1);
        }
        catch (Mage_Core_Exception $ex) {
            $messages = array_unique(explode("\n", $ex->getMessage()));
            foreach($messages as $message) {
                Mage::getSingleton('core/session')->addError(Mage::helper('core')->escapeHtml($message));
            }
            Mage::register('favorite_delete_success', 0);
        }
        catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError($this->__('An error has occurred. Please try again later'));
            Mage::register('favorite_delete_success', 0);
        }

        $this->renderJsonLayout();
    }
}
