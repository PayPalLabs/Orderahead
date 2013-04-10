<?php
/**
 * Featuredcategory
 *
 * @package      :  Paypal_Featuredcategory
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

class Paypal_Featuredcategory_CategoryController extends Paypal_Core_Controller_Front_Action{
	
	public function listAction()
	{
		try{
			$this->loadLayout();
			$this->renderJsonLayout();
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			return false;
		}
	}
}