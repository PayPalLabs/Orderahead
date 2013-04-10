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
class Paypal_Checkin_Adminhtml_CheckinController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction(){
		$websiteId =  Mage::helper("paypal_checkin")->getWebsiteId(Mage::getSingleton('core/session')->getCheckinAuthWebsite());
		$refreshToken = Mage::helper("paypal_checkin")->getRefreshToken($websiteId);
		$redirectURI = explode("?", Mage::helper('core/url')->getCurrentUrl());
		
		$code =  $this->getRequest()->getParam("code",false);
		if($code){
			$nvp = Mage::getModel("paypal_checkin/api_auth")
			->callTokenserviceAuthorize(
					$code,
					Mage::helper('paypal_checkin')->getClientId($websiteId),
					Mage::helper('paypal_checkin')->getClientSecret($websiteId),
					$redirectURI[0]
			);
			if(array_key_exists('refresh_token',$nvp)){
				Mage::helper("paypal_checkin")->saveRefreshToken($nvp['refresh_token'],$websiteId);
				Mage::getSingleton('adminhtml/session')->addSuccess("Refresh token saved successfully");
				//system_config/edit/section/paypal/website/saladstop/
				$this->_redirect("*/system_config/edit/section/paypal/website/".Mage::getSingleton('core/session')->getCheckinAuthWebsite());
			}else{
				//error_description' => 'redirect uri mismatch between auth code and token', 'error' => 'invalid_request'
				Mage::helper("paypal_checkin")->saveRefreshToken('',$websiteId);
				Mage::getSingleton('adminhtml/session')->addError("Refresh token save failed");
				Mage::getSingleton('adminhtml/session')->addError($nvp['error']." : ".$nvp['error_description']);
				$this->_redirect("*/system_config/edit/section/paypal/website/".Mage::getSingleton('core/session')->getCheckinAuthWebsite());
			}
		}
	}
}