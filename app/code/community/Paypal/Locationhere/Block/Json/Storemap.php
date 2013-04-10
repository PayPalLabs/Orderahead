<?php
/**
 * PayPal Locationhere
 *
 * @package      :  PayPal_Locationhere
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
class Paypal_Locationhere_Block_Json_Storemap extends Mage_Core_Block_Template {

    public function _toHtml(){
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        $result = array();
        if (!$orderId) {
          $orderId = $this->getRequest()->getParam('order_id');
          $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        }
        else {
          $order = Mage::getModel('sales/order')->load($orderId);
        }
        if ($order->getId()) {
		        $result = $this->getStoremapDetails($order);
		        return Mage::helper('core')->jsonEncode($result);
		    }
    		return Mage::helper('core')->jsonEncode($result);
    }
    
    private function getStoremapDetails($order){
        $shippingAddress = $order->getShippingAddress();//return shipping address
        //return  store address
        $storeStreetArray = $shippingAddress->getStreet();
        $storeStreet = '';
        
        foreach($storeStreetArray as $street){
            $storeStreet .= ' ' . $street;
        }
        $storeCity = $shippingAddress->getCity();
        
        $array = array(
          'name' => $shippingAddress->getCompany(),
          'address' => trim($storeStreet . ' ' . $storeCity),
          'logo' => $this->getSkinUrl(Mage::getStoreConfig('design/header/logo_src')),
          'phonenum' => $shippingAddress->getTelephone(),
          'latitude' => "1.295675458510728", //Mage::getModel('paypal_locationcheckin/locationcheckin')->getLatitude(),
          'longtitude' => "103.85899543762207", //Mage::getModel('paypal_locationcheckin/locationcheckin')->getLongtitude()
        );

        return $array;
    }
}