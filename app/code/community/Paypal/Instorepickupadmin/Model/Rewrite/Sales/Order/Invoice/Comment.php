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
class Paypal_Instorepickupadmin_Model_Rewrite_Sales_Order_Invoice_Comment extends Mage_Sales_Model_Order_Invoice_Comment
{
    /**
     * Before object save
     *
     * @return Mage_Sales_Model_Order_Invoice_Comment
     */
    protected function _beforeSave()
    {
        $session = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn()) { //only for login admin user
            $user = $session->getUser();
            if (!$this->getId()) { //only for new entry
                $this->setData('username', $user->getFirstname() . ' ' . $user->getLastname());
                $role = $user->getRole();
                $this->setData('userrole', $role->getRoleName());

                $orderId = Mage::app()->getRequest()->getParam('id');
                $hash = Mage::app()->getRequest()->getParam('hash');
                if(!empty($orderId) && !empty($hash)){
                    if(Mage::helper('paypal_instorepickupadmin')->checkSecurityCode($hash,$orderId)){
                        $this->setData('is_security', '1');
                    }
                    else{
                        $this->setData('is_security', '0');
                    }
                }
            }
        }
        parent::_beforeSave();
        return $this;
    }
}
