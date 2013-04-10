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
class Paypal_Checkin_Model_Api_Auth extends Paypal_Checkin_Model_Api_Abstract {

	public function callTokenserviceAuthorize($code, $clientId, $clientSecret, $redirectURI){
		try{
			$uri = 'webapps/auth/protocol/openidconnect/v1/tokenservice';
			$params = array (
					"grant_type" => "authorization_code",
					"code" => $code,
					"client_id" => $clientId,//"e2298ec0cffe039c0b6421de9ee07cff",
					"client_secret" =>	$clientSecret,//"3ac48be7e5e078f7",
					"redirect_uri" => $redirectURI
			);
			$response = $this->httpRequest($uri, $params, null, Zend_Http_Client::POST);
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

	public function callTokenserviceRefresh($websiteId = ''){
		try{
			$checkinSession = Mage::getSingleton('checkout/session');
				
			if($checkinSession->getAccessToken() == '' || time() >= $checkinSession->getExpiresAt()){
				$websiteId =  ($websiteId == '')?Mage::app()->getStore()->getWebsiteId():$websiteId;
				// get variables
				$uri = 'webapps/auth/protocol/openidconnect/v1/tokenservice';
				$clientId = Mage::helper('paypal_checkin')->getClientId($websiteId);
				$clientSecret = Mage::helper('paypal_checkin')->getClientSecret($websiteId);
				$refreshToken = Mage::helper('paypal_checkin')->getRefreshToken($websiteId);
                                //$refreshToken = 'UcieM8beWFU1auOtmz/7oYx3PhScoeM2+AWV6JAP2OY5BvUD1+Cw4LoIFtxNR+mI747mtdEpnKqjdBug';
                                
				// prepare parameters
				$params = array (
						"grant_type" => "refresh_token",
						"refresh_token" => $refreshToken,
						"client_id" => $clientId,
						"client_secret" =>	$clientSecret,
				);

				// get webservice response
				$response = $this->httpRequest($uri, $params, null, Zend_Http_Client::POST);

				if(Mage::helper('paypal_checkin')->responseHasErrors($response)){
					throw new Exception($response['message'], $response['errorCode']);
				}

				$this->setAccessToken($response['access_token']);
				Mage::getSingleton('checkout/session')->setAccessToken($response['access_token']);
				$this->setExpiresAt($response['expires_in'] + time());
				Mage::getSingleton('checkout/session')->setExpiresAt($response['access_token']);
			}
		}catch(Zend_Http_Client_Exception $e){
			Mage::logException($e);
			throw $e;
		}catch(Exception $e){
			Mage::logException($e);
			throw $e;
		}
		return $this->getAccessToken();
	}



	public function callUserinfo(){
		try{
			// get the access token and refresh it if needed
			$accessToken = $this->callTokenserviceRefresh();
	
			$uri = 'webapps/auth/protocol/openidconnect/v1/userinfo';
	
			$headers = array (
					'Content-type'	=>	'application/x-www-form-urlencoded',
			);
			$parameters = array (
					"schema"	=> 	'openid',
					"access_token" => $accessToken,
			);
	
			$response = $this->httpRequest($uri, $parameters, $headers, Zend_Http_Client::GET);
			
			if(Mage::helper('paypal_checkin')->responseHasErrors($response)){
				throw new Exception($response['message'], $response['errorCode']);
			}
			// save access token in session
			Mage::getSingleton('checkout/session')->setUserInfo($response);
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