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
class Paypal_Checkin_Helper_Data extends Mage_Core_Helper_Abstract
{

	const XML_PATH_PAYPAL_CHECKIN_ENABLED = "payment/paypal_checkin/active";
	const XML_PATH_REFRESH_TOKEN = "payment/paypal_checkin/refresh_token";
	const XML_PATH_CLIENT_ID = "payment/paypal_checkin/client_id";
	const XML_PATH_CLIENT_SECRET = "payment/paypal_checkin/client_secret";
	const XML_PATH_WEBSITE_ROOT_URL = 'payment/paypal_checkin/webservice_root_url';
	const XML_PATH_PAYPAL_CHECKIN_SHIPPING_METHOD = "payment/paypal_checkin/shipping_method";
	const XML_PATH_PAYPAL_MERCHANT_EMAIL = "payment/paypal_checkin/merchant_email";
	
	const ORDER_STATUS_CODE_PAID = "paid";
	const ORDER_STATUS_LABEL_PAID = "Paid";
	
	const ORDER_STATUS_CODE_DELIVERED = "delivered";
	const ORDER_STATUS_LABEL_DELIVERED = "Delivered";
	
	const ORDER_STATUS_CODE_REJECTED = "rejected";
	const ORDER_STATUS_LABEL_REJECTED = "Rejected";
	
	const ORDER_STATUS_CODE_PARTIALLY_DELIVERED = "partially_delivered";
	const ORDER_STATUS_LABEL_PARTIALLY_DELIVERED = "Partially Delivered";

	public function getIsenabled($websiteId = null){
		if(is_null($websiteId)){
			$websiteId = Mage::app()->getStore()->getWebsiteId();
		}
		$isenabled = Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_PAYPAL_CHECKIN_ENABLED);
		return $isenabled;
	}
	
	
	public function getRefreshToken($websiteId = null){
		if(is_null($websiteId)){
			$websiteId = Mage::app()->getStore()->getWebsiteId();
		}
		
		$refreshToken = "";
		if($websiteId !=0){
			$refreshToken = Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_REFRESH_TOKEN);
		}
		return $refreshToken;
	}

	public function getStoreId($storeCode){
		if($storeCode === 0){
			return 0;
		}
		$stores = array_keys(Mage::app()->getStores());
		foreach($stores as $id){
			$store = Mage::app()->getStore($id);
			if($store->getCode()==$storeCode) {
				return $store->getId();
			}
		}
		return false;
	}

	public function getWebsiteId($websiteCode){
		if($websiteCode === 0){
			return 0;
		}
		$websites = array_keys(Mage::app()->getWebsites());
		foreach($websites as $id){
			$website = Mage::app()->getWebsite($id);
			if($website->getCode()==$websiteCode) {
				return $website->getId();
			}
		}
		return false;
	}

	public function saveRefreshToken($token, $websiteId){
		if($websiteId != 0){
			Mage::getConfig()->saveConfig(self::XML_PATH_REFRESH_TOKEN, $token, 'websites', $websiteId);
		}
	}

	public function getWebserviceRootUrl($websiteId = null){
		$webserviceRootUrl = '';
		if(!($websiteId === null)){
			$webserviceRootUrl = Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_WEBSITE_ROOT_URL);
		}
		return $webserviceRootUrl;
	}
	
	public function getClientId($websiteId = null){
		$clientId = 0;
		if(!($websiteId === null)){
			$clientId = Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_CLIENT_ID);
		}
		return $clientId;
	}
	
	public function getClientSecret($websiteId = null){
		$clientSecret = 0;
		if(!($websiteId === null)){
			$clientSecret = Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_CLIENT_SECRET);
		}
		return $clientSecret;
	}
	
	public function getShippingMethod($websiteId = null){
		$shippingMethod = false;
		$storeId = Mage::app()->getStore()->getId(); 
		if(!($websiteId === null)){
			$shippingMethod = Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_PAYPAL_CHECKIN_SHIPPING_METHOD);
			if( (!(isset($shippingMethod))) && (!$storeId == 0) ){
				$shippingMethod = Mage::getStoreConfig(self::XML_PATH_PAYPAL_CHECKIN_SHIPPING_METHOD, $storeId);
			}
		}
		return $shippingMethod;
	}
	
	public function getMerchantEmail($websiteId = null){
		$merchantEmail = false;
		$storeId = Mage::app()->getStore()->getId();
		if(!($websiteId === null)){
			$merchantEmail = Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_PAYPAL_MERCHANT_EMAIL);
			if( (!(isset($shippingMethod))) && (!$storeId == 0) ){
				$merchantEmail = Mage::getStoreConfig(self::XML_PATH_PAYPAL_MERCHANT_EMAIL, $storeId);
			}
		}
		return $merchantEmail;
	}
	
	public function getOrderItems($order){
		$arrayItems = array();
		$items = $order->getAllItems();
		foreach ($items as $itemId => $item){
			$tempItems = array(
							'name' 		=> $item->getName(),
							'unitPrice' => $item->getPrice(),
							'quantity'	=> $item->getQtyToInvoice()
					);
			$arrayItems[]=$tempItems;
		}
		return $arrayItems;
	}
	
	public function createInvoice($order, $ppInvoiceId="NA", $ppTransactionId="NA"){
		$isInvoiceCreated = false;
		try{
			if(!$order->canInvoice()){
				return $isInvoiceCreated;
			}
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
			if (!$invoice->getTotalQty()) {
				Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
			}
			$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
			$invoice->register();
			$comment = "Paypal Invoice id : ".$ppInvoiceId;
			$comment = $comment."<br/> Paypal Transaction id : ".$ppTransactionId;
			$order->addStatusHistoryComment($comment)
					->setIsVisibleOnFront(true)
					->setIsCustomerNotified(true);
			$transactionSave = Mage::getModel('core/resource_transaction')
								->addObject($invoice)
								->addObject($invoice->getOrder());
			$transactionSave->save();
			$isInvoiceCreated = true;
		}catch(Exception $e){
			Mage::log("Invoice created failed !!");
			Mage::logException($e);
			throw $e;
		}
		Mage::log("Invoice created : ".$isInvoiceCreated);
		return $isInvoiceCreated;
	}
	
	public function responseHasErrors($json){
		$hasError = false;
		if ( (isset($json['errorCode'])) || 
					(isset($json['errorType'])) ||
						(isset($json['message'])) ||
							(isset($json['developerMessage'])) ||
								(isset($json['correlationId']))
				){
			Mage::log("Error : ".var_export($json, true));
			$hasError = true;
		}
		return $hasError;
	}
	
	public function cancelOrder($order){
		if(isset($order)){
			//Cancel the order
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED); 
			$order->save();
		}
	}
	
	public function getProductOptions($item, $displayLabels=false){
		$optionsText = array();
		$options = array();
		try{
			$productOptions = $item->getProductOptions();
			if (isset($productOptions['options'])) {
				$options = array_merge($options, $productOptions['options']);
			}
			if (isset($productOptions['additional_options'])) {
				$options = array_merge($options, $productOptions['additional_options']);
			}
			if (!empty($productOptions['attributes_info'])) {
				$options = array_merge($productOptions['attributes_info'], $options);
			}
			foreach($options as $option){
				$optionsText[] = ($displayLabels ? ($option['label'].' : '.$option['value']) : ($option['value']));
			}
		}catch(Exception $e){
			Mage::logException($e);
		}
		return implode(",", $optionsText);
	}
	
}