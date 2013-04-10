<?php
/**
 * PayPal Checkin
 *
 * @package      :  PayPal_Checkin
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 */
class Paypal_Checkin_Model_Observer{
	public function salesOrderSaveBefore(Varien_Event_Observer $observer){
		$order = $observer->getOrder();
		try{
			$paypal_customer_id = Mage::getSingleton('core/session')->getCheckinCustomerId();
			if(isset($paypal_customer_id))
				if($paypal_customer_id != '')
					$order->setPaypalCustomerId($paypal_customer_id);
		}
		catch(Exception $e){
			_redirect('/');
		}
		return $order;
	}

    /*
     * add default shipping method to quote
     */
    public function addShipping(){
        $quote = Mage::getSingleton('checkout/session')->getQuote(); //get quote
                        
        $shippingAddress = $quote->getShippingAddress();
        
        if(is_null($shippingAddress->getCountry())){
            $country = Mage::getStoreConfig('shipping/origin/country_id');
            $state = Mage::getStoreConfig('shipping/origin/region_id');
            $postCode = Mage::getStoreConfig('shipping/origin/postcode');
            $websiteId = Mage::helper("paypal_checkin")->getWebsiteId(Mage::app()->getRequest()->getParam('website', 0));
            $shippingMethod = Mage::helper("paypal_checkin")->getShippingMethod($websiteId);

            $shippingAddress->setShippingMethod($shippingMethod)
                            ->setCountryId($country)
                            ->setRegionId($state)
                            ->setPostcode($postCode)
                            ->setCollectShippingRates(true);
            
            $quote->save();
        }   
        
        Mage::getSingleton('checkout/session')->resetCheckout();
    }	
}