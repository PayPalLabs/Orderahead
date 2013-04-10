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
class Paypal_Instorepickupadmin_Adminhtml_Instorepickupadmin_OrderController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init action
     */
    protected function _initAction()
    {
        if(!Mage::getSingleton('admin/session')->isAllowed('instorepickupadmin')){
            $this->_redirect('*/*/denied');
        }
        return $this;
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Search action
     * Render form search
     */
    public function searchAction()
    {
        $this->_initAction();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Search Result  Action
     * Render order list search result
     */
    public function searchresultAction()
    {
        $this->_initAction();
        $collection = Mage::getResourceModel('sales/order_collection')->addAddressFields();
        $orderId = $this->getRequest()->getPost('order_id');
        if($orderId){
            $collection->addFieldToFilter('increment_id',array('like'=>'%'.$orderId.'%'));
        }

        $keywords = $this->getRequest()->getPost('email_firstname_lastname');
        if($keywords){

            $condition = array();
            $condition[] = 'billing_o_a.firstname  like ?';
            $condition[] = 'billing_o_a.lastname  like ?';
            $condition[] = 'billing_o_a.email  like ?';
            $where = implode(' OR ',$condition);
            $collection->getSelect()->where($where, '%'.$keywords.'%');
        }

        $orderState = $this->getRequest()->getPost("order_state");
        if($orderState){
            $collection->addFieldToFilter('state',$orderState);
        }

        if (Mage::helper('core')->isModuleEnabled('Paypal_Pickup')) {
            $placeId = $this->getRequest()->getPost("place_id");
            if($placeId){
                $collection->addFieldToFilter('place_id',$placeId);
            }
        }
        $collection->setOrder('created_at', 'desc');
        Mage::register('order_collection',$collection);

        if($collection->getSize() == 1){
            $firstOrder = $collection->getFirstItem();
            $this->_redirect('*/*/detail',array('id'=>$firstOrder->getId()));
        }
        else {
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    /**
     * Detail action
     * Render order detail
     */
    public function detailAction()
    {
        $this->_initAction();
        $orderId = $this->getRequest()->getParam('id');
        $hash = $this->getRequest()->getParam('hash');
        if($this->_validateHash($orderId,$hash)){
            if($this->_initOrder()){
                    $this->loadLayout();
                    $this->renderLayout();
            }
            else{
                $this->_getSession()->addError('Order not exist');
                $this->_redirect('*/*/search');
            }
        }
        else{
            $this->_getSession()->addError('Invalidate Secret Key');
            $this->_redirect('*/*/search');
        }
    }

    /**
     * save order action
     */
    public function saveAction()
    {
        $this->_initAction();
        if ($order = $this->_initOrder()) {
            try {
                // add order comment
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $data['is_customer_notified'] = true;
                $data['is_visible_on_front'] = true;
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot add order history.'));
            }
            $this->_redirect('*/*/detail',array('_current'=> true));
        }
    }

    /**
     * Denied json action
     * Render order detail
     */
    public function deniedJsonAction()
    {

    }

    /**
     * Denied action
     * Render order detail
     */
    public function deniedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Login action
     */
    public function loginAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Logout action
     */
    public function logoutAction()
    {
        /** @var $adminSession Mage_Admin_Model_Session */
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());
        $adminSession->addSuccess(Mage::helper('adminhtml')->__('You have logged out.'));
        $this->_redirect('adminhtml/instorepickupadmin_order');
    }

    /**
     * Controller predispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        // turn off secret key
        Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
        parent::preDispatch();
    }

    protected function getCurrentUser()
    {
        return Mage::getSingleton('admin/session')->getUser();
    }

    /**
     * Validate secret key
     *
     * @param string $orderId
     * @param string $hash
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _validateHash($orderId,$hash)
    {
        $result = true;
        if(is_null($hash)){
            $result = true;
        }
        else{
            $result = Mage::helper('paypal_instorepickupadmin')->checkSecurityCode($hash,$orderId);
        }
        return $result;
    }
}