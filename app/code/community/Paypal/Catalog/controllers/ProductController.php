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
class Paypal_Catalog_ProductController extends Paypal_Core_Controller_Front_Action {

    public function listtemplateAction(){
        try {
            $this->loadLayout();
            $this->renderLayout();
        }  catch (Mage_Core_Exception $e){
            Mage::logException($e);
            return false;
        }
    }
        
	public function listAction()
	{
		try {
            $params = $this->getRequest()->getParams();
            if (isset($params['id']) && is_numeric($params['id'])) {
                $categoryId = $params['id'];
                $category = Mage::getModel('catalog/category')->load($categoryId);
                if ($category->getId()) {
                    Mage::register('pp_current_category', $category);
                }
                else {
                    Mage::getSingleton('core/session')->addError(Mage::helper('paypal_catalog')->__('Invalid category id'));
                }
            }
            else {
                Mage::getSingleton('core/session')->addError(Mage::helper('paypal_catalog')->__('Invalid category id'));
            }
			$this->loadLayout();
			$this->renderJsonLayout();
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			return false;
		}
	}
	
	public function viewAction()
	{
		try {
            $params = $this->getRequest()->getParams();
            $product = null;
            if (isset($params['id']) && $params['id'] != '' && is_numeric($params['id'])) {
                $product = Mage::getModel("catalog/product")->load($params['id']);
                if ($product && $product->getId() == $params['id']) {
                    Mage::register('pp_current_product', $product);
                }
                else {
                    Mage::getSingleton('core/session')->addError(Mage::helper('paypal_catalog')->__('Product not found'));
                }
            }
            else {
                Mage::getSingleton('core/session')->addError(Mage::helper('paypal_catalog')->__('Invalid product id'));
            }

			$this->loadLayout();
            $this->renderJsonLayout();
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			return false;
		}
	}
        
    public function viewtemplateAction(){
        try{
            $this->loadLayout();
            $this->renderLayout();
        }catch(Exception $e){
            Mage::logException($e);
            return false;
        }
    }
}
