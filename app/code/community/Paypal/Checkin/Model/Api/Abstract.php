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
class Paypal_Checkin_Model_Api_Abstract extends Mage_Core_Model_Abstract{
	
	const DEFAULT_TIMEOUT = 30;
	
	const NORMAL_TIMEOUT = 45;
	
	const MAX_TIMEOUT = 60;
	
	public function httpRequest($endpoint, $params, $headers = null, $method = 'GET', $timeout = self::DEFAULT_TIMEOUT){
		try{
			$response = "";
			$iClient = new Varien_Http_Client();
			
			$baseUrl = (Mage::app()->getStore()->getId() == 0) ? 
						Mage::helper('paypal_checkin')->getWebserviceRootUrl(Mage::helper("paypal_checkin")->getWebsiteId(Mage::getSingleton('core/session')->getCheckinAuthWebsite())) :
						Mage::helper('paypal_checkin')->getWebserviceRootUrl(Mage::app()->getStore()->getWebsiteId());
			$endpoint = 'https://www.paypal.com/'.$endpoint;
			
			Mage::log(" End point : ".$endpoint);
			$iClient->setUri($endpoint)
				->setMethod($method)
				->setConfig(array(
						'maxredirects'=>0,
						'timeout'=>$timeout,
				));
			
			if(!is_null($headers)){
				$iClient->setHeaders($headers);
			}
			
			if($method == 'GET'){
				$iClient->setParameterGet($params);
			} else if($method == 'POST'){
				if(is_array($params)){
					$iClient->setParameterPost($params);
				}else if(is_string($params)){
					$iClient->setRawData($params, "application/json;charset=UTF-8");
				}else{
					throw new Zend_Http_Client_Exception("Post Parameters niether JSON nor String",400);
				}
			}

			Mage::log(" Headers : ".var_export($headers, true));
			Mage::log(" Parameters : ".var_export($params, true));
			
			$responseJson = $iClient->request();

			$response = Mage::helper('core')->jsonDecode($responseJson->getBody());
			
            Mage::log(" Response : ".var_export($response, true));                                          
                                                      
		}catch(Zend_Http_Client_Exception $e){
			Mage::logException($e);
			throw $e;
		}
                
		return $response;
	}
	
	public function curlRequest($endpoint, $params, $headers = null, $method = 'GET'){
		try{
			$baseUrl = (Mage::app()->getStore()->getId() == 0) ?
			Mage::helper('paypal_checkin')->getWebserviceRootUrl(Mage::helper("paypal_checkin")->getWebsiteId(Mage::getSingleton('core/session')->getCheckinAuthWebsite())) :
			Mage::helper('paypal_checkin')->getWebserviceRootUrl(Mage::app()->getStore()->getWebsiteId());
			$endpoint = $baseUrl.'/'.$endpoint;
			// Initialize session and set URL.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $endpoint);
			
			// Set so curl_exec returns the result instead of outputting it.
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			//Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			//Set headers
			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
			
			if($method == 'GET'){
				curl_setopt($ch, CURLOPT_HTTPGET);
			}else{
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
			
			// Get the response and close the channel.
			$response = curl_exec($ch);
			curl_close($ch);
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $response;
	}
	
}

