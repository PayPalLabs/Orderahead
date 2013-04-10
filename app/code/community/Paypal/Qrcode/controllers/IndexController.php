<?php
/**
 * Qrcode
 *
 * @package      :  Paypal_Qrcode
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

class Paypal_Qrcode_IndexController extends Paypal_Core_Controller_Front_Action
{
    public function indexAction() {
        Mage::getModel('paypal_qrcode/qrcode')->generateQrcode('Hello. Is it me youre looking for?', 'hello');
    }
}
