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
class Paypal_Checkin_Block_Adminhtml_Linkcontent extends Mage_Core_Block_Html_Link
implements Mage_Widget_Block_Interface{


	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$website = $this->getRequest()->getParam('website', 0);
		$websiteId =  Mage::helper("paypal_checkin")->getWebsiteId($website);
		Mage::getSingleton('core/session')->setCheckinAuthWebsite($website);
		$refreshToken = Mage::helper("paypal_checkin")->getRefreshToken($websiteId);
		$clientId = Mage::helper("paypal_checkin")->getClientId($websiteId);
		$html = $refreshToken;
		
		
		$hrefURL = "https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize?scope=https://uri.paypal.com/services/paypalhere&response_type=code&redirect_uri=".Mage::getBaseUrl()."/admin/checkin/index&client_id=".$clientId;
		$html = $html. "<br/><a href=\"$hrefURL\">Get new refresh token!</a>";
		return $html;
	}

}