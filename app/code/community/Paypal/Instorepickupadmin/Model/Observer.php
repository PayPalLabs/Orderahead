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
class Paypal_Instorepickupadmin_Model_Observer
{
    /**
     * Handler for controller_action_predispatch_adminhtml_index_login
     *
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
    public function actionPreDispatchAdmin($observer)
    {
        $request = Mage::app()->getRequest();
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        if(strpos($currentUrl,'instorepickupadmin_order') !== false){
            $session = Mage::getSingleton('admin/session');
            /** @var $session Mage_Admin_Model_Session */
            $user = $session->getUser();
            if (!$user || !$user->getId()) {
                $request->setParam('forwarded', true)
                    ->setRouteName('adminhtml')
                    ->setControllerName('instorepickupadmin_order')
                    ->setActionName('login')
                    ->setDispatched(false);
                    return false;
            }
        }
        return true;
    }

    /**
     * Handler for sales_order_status_history_save_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function orderStatusHistorySaveBefore($observer)
    {
        $session = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn()) { //only for login admin user
            $user = $session->getUser();
            $history = $observer->getEvent()->getStatusHistory();
            if (!$history->getId()) { //only for new entry
                $history->setData('username', $user->getFirstname() . ' ' . $user->getLastname());
                $role = $user->getRole();
                $history->setData('userrole', $role->getRoleName());

                $orderId = Mage::app()->getRequest()->getParam('id');
                $hash = Mage::app()->getRequest()->getParam('hash');
                if(!empty($orderId) && !empty($hash)){
                    if(Mage::helper('paypal_instorepickupadmin')->checkSecurityCode($hash,$orderId)){
                        $history->setData('is_security', '1');
                    }
                    else{
                        $history->setData('is_security', '0');
                    }
                }
            }
        }
    }
}
