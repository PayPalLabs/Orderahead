<?php
/**
 * Sales
 *
 * @package      :  Paypal_Sales
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

class Paypal_Sales_OrderController extends Paypal_Core_Controller_Front_Action
{
    public function recentAction() {
        try {
            $paypal_customer_id = Mage::getSingleton('core/session')->getCheckinCustomerId();
            if (!isset($paypal_customer_id) || $paypal_customer_id == '') {
                Mage::getSingleton('core/session')->addError('Invalid user ID');
            }
            else {
                $orders = Mage::getResourceModel('sales/order_collection')
                    ->addAttributeToSelect('*')
                    ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                    ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                    ->addAttributeToFilter('paypal_customer_id', $paypal_customer_id)
                    ->addAttributeToSort('created_at', 'desc')
                    ->setPageSize('5')
                    ->load()
                ;
                Mage::register('current_order_list', $orders);
            }

            $this->loadLayout();
            $this->renderJsonLayout();
            
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }
    
    public function recenttemplateAction(){
        try {
            $this->loadLayout();
            $this->renderLayout();
        } catch(Exception $e){
            Mage::logException($e);
            return false;
        }
    }

    public function detailsAction() {
        try {
            $order_id = $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
            if ($order->getIncrementId() === $order_id) {
                Mage::register('current_order', $order);
            }
            else {
                Mage::getSingleton('core/session')->addError('Invalid order ID');
            }

            $this->loadLayout();
            $this->renderJsonLayout();
        } catch(Exception $e){
            Mage::logException($e);
            return false;
        }
    }
    
    public function detailstemplateAction(){
        try{
            $this->loadLayout();
            $this->renderLayout();
        } catch(Exception $e){
            Mage::logException($e);
            return false;
        }
    }
}
