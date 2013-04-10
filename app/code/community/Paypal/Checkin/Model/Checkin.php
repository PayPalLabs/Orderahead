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
class Paypal_Checkin_Model_Checkin extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'paypal_checkin';
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = false;
	protected $_canUseForMultishipping  = false;
}