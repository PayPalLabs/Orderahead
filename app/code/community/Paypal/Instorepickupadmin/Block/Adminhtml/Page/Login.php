<?php
/**
 * Instore Pickup Admin
 *
 * @package      :  Paypal_Instorepickupadmin
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
class Paypal_Instorepickupadmin_Block_Adminhtml_Page_Login extends Paypal_Instorepickupadmin_Block_Adminhtml_Section
{

    public function __construct()
    {
        $this->setSectionId(Paypal_Instorepickupadmin_Helper_Data::SECTION_LOGIN);
        $this->setSectionTitle($this->__('Login'));
    }
}
