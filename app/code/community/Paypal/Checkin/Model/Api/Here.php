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
class Paypal_Checkin_Model_Api_Here extends Paypal_Checkin_Model_Api_Abstract {

	public function callInvoices($items){
		try{
			// get the access token and refresh it if needed
			$accessToken = Mage::getModel('paypal_checkin/api_auth')->callTokenserviceRefresh();
			
			$uri = 'webapps/hereapi/merchant/v1/invoices';
			$headers = array(
				'Authorization'	=>	'Bearer '.$accessToken,
				'Content-Type'	=>	'application/json;charset=UTF-8',
			);
			
			$locationDetails = Mage::getSingleton('core/session')->getCheckinLocationDetails();
			$parameters = Array();
			$merchantInfo = Array();
			$merchantInfo["businessName"] = $locationDetails['name'];
			$merchantInfo["address"] = $locationDetails['address'];
			
			$parameters["merchantEmail"] = Mage::getSingleton('core/session')->getCheckinMerchantEmail();
			$parameters["merchantInfo"] = $merchantInfo;
			$parameters["currencyCode"] = Mage::app()->getStore()->getCurrentCurrencyCode();
			$parameters["items"] = $items;
			$parameters["invoiceDate"] = date("c",Mage::getModel('core/date')->timestamp(time()));
			$parameters["paymentTerms"] = "DueOnReceipt";
			$parameters = Mage::helper('core')->jsonEncode($parameters);
			$response = $this->httpRequest($uri, $parameters, $headers, Zend_Http_Client::POST);
			if(Mage::helper('paypal_checkin')->responseHasErrors($response)){
				throw new Exception($response['message'], $response['errorCode']);
			}
		}catch(Zend_Http_Client_Exception $e){
			Mage::logException($e);
			throw $e;
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}

		return 	$response['invoiceID'];
	}
	
	public function callPay($invoiceId){
		try{
			// get the access token and refresh it if needed
			$accessToken = Mage::getModel('paypal_checkin/api_auth')->callTokenserviceRefresh();
			
			$uri = 'webapps/hereapi/merchant/v1/pay';
			$headers = array (
				'Authorization'	=>	'Bearer '.$accessToken,
				'Content-Type'	=>	'application/json;charset=UTF-8',
			);
	                
			$tabId = Mage::getSingleton('core/session')->getCheckinTabId();
	                
			$parameters = json_encode(array (
				'paymentType'	=>	'tab',
				'invoiceId'		=>	$invoiceId,
				'tabId'			=>	$tabId,
			));
	
			$response = $this->httpRequest($uri, $parameters, $headers, Zend_Http_Client::POST, 
								Paypal_Checkin_Model_Api_Abstract::NORMAL_TIMEOUT);
			if(Mage::helper('paypal_checkin')->responseHasErrors($response)){
				throw new Exception($response['message'], $response['errorCode']);
			}
		}catch(Zend_Http_Client_Exception $e){
			Mage::logException($e);
			throw $e;
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $response['transactionNumber'];
	}
	
	public function callLocations($_locationid, $_tabid = ''){
		try{
			// get the access token and refresh it if needed
			$accessToken = Mage::getModel('paypal_checkin/api_auth')->callTokenserviceRefresh();
			
	
	                
			$uri = 'webapps/hereapi/merchant/v1/locations';
			$uri = ($_locationid <> "") ? ($uri."/".$_locationid) : $uri;
			$headers = array (
					'Authorization'	=>	'Bearer '.$accessToken,
					'Content-Type'	=>	'application/json',
			);
			$parameters = array();
			$response = $this->httpRequest($uri, $parameters, $headers, Zend_Http_Client::GET);
			if(Mage::helper('paypal_checkin')->responseHasErrors($response)){
				throw new Exception($response['message'], $response['errorCode']);
			}
		}catch(Zend_Http_Client_Exception $e){
			Mage::logException($e);
			throw $e;
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $response;
	}
	
	public function callTab($locationId, $tabId){
		try{
			// get the access token and refresh it if needed
			$accessToken = Mage::getModel('paypal_checkin/api_auth')->callTokenserviceRefresh();
				
			$uri = 'webapps/hereapi/merchant/v1/locations';
			$uri = ($locationId <> "") ? ($uri."/".$locationId) : $uri;
			$uri = ($tabId <> "") ? ($uri."/tabs/".$tabId) : $uri;
			$headers = array (
					'Authorization'	=>	'Bearer '.$accessToken,
					'Content-Type'	=>	'application/json',
			);
			 
			$parameters = json_encode(array (
			));
	
			$response = $this->httpRequest($uri, $parameters, $headers, Zend_Http_Client::GET,
					Paypal_Checkin_Model_Api_Abstract::NORMAL_TIMEOUT);
			if(Mage::helper('paypal_checkin')->responseHasErrors($response)){
				throw new Exception($response['message'], $response['errorCode']);
			}
		}catch(Zend_Http_Client_Exception $e){
			Mage::logException($e);
			throw $e;
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $response;
	}
	
	public function callTabs($locationId){
		try{
			// get the access token and refresh it if needed
			$accessToken = Mage::getModel('paypal_checkin/api_auth')->callTokenserviceRefresh();
		
			$uri = 'webapps/hereapi/merchant/v1/locations';
			$uri = ($locationId <> "") ? ($uri."/".$locationId.'/tabs') : $uri;
			$headers = array (
					'Authorization'	=>	'Bearer '.$accessToken,
					'Content-Type'	=>	'application/json',
			);
		
			$parameters = json_encode(array (
			));
		
			$response = $this->httpRequest($uri, $parameters, $headers, Zend_Http_Client::GET,
					Paypal_Checkin_Model_Api_Abstract::NORMAL_TIMEOUT);
			if(Mage::helper('paypal_checkin')->responseHasErrors($response)){
				throw new Exception($response['message'], $response['errorCode']);
			}
		}catch(Zend_Http_Client_Exception $e){
			Mage::logException($e);
			throw $e;
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $response;
	}
}