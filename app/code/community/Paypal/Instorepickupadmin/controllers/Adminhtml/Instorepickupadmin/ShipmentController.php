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
require_once ('Mage'.DS.'Adminhtml'.DS. 'controllers' .DS. 'Sales' .DS .'Order'.DS.'ShipmentController.php');
class Paypal_Instorepickupadmin_Adminhtml_Instorepickupadmin_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{
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
     * Initialize invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice($data)
    {
        $invoice = false;
        $itemsToInvoice = 0;
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            if (!$invoice->getId()) {
                $this->_getSession()->addError($this->__('The invoice no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order = $this->getOrder();
            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
            /**
             * Check invoice create availability
             */
            if (!$order->canInvoice()) {
                $this->_getSession()->addError($this->__('The order does not allow creating an invoice.'));
                return false;
            }
            $savedQtys = $data['items'];
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
            if (!$invoice->getTotalQty()) {
                Mage::throwException($this->__('Cannot create an invoice without products.'));
            }
        }

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * save order action
     */
    public function saveAction()
    {
        $this->_initOrder();
        $orderId = $this->getRequest()->getParam('id');
        $this->getRequest()->setParam('order_id',$orderId);
        $data = $this->getRequest()->getPost('shipment');
        $data['comment_customer_notify'] = "1";
        $data['is_visible_on_front'] = "1";
        $data['send_email'] = "1";

        $checked = false;
        if(!empty($data['items'])){
            foreach($data['items'] as $it){
                if($it > 0){
                    $checked = true;
                }
            }
        }
        // save shipment
        try{
            if($checked){
                $shipment = $this->_initShipment();
                Mage::unregister('current_shipment');
                $this->_autoCreateInvoice($data);
                $this->getRequest()->setPost('shipment',$data);
                parent::saveAction();
            }
            else{
                $comment = $data['comment_text'];
                if(!empty($comment)){
                    $order = $this->getOrder();
                    if(!empty($order)){
                        try {
                            $order->addStatusHistoryComment($comment)
                                ->setIsVisibleOnFront(true)
                                ->setIsCustomerNotified(true);
                            $order->save();
                        }
                        catch (Mage_Core_Exception $e) {
                            $this->_getSession()->addError($e->getMessage());
                        }
                        catch (Exception $e) {
                            $this->_getSession()->addError($this->__('Cannot add order history.'));
                        }
                    }
                }
                $this->_autoCreateInvoice($data);
            }

        }
        catch(Mage_Core_Exception $e){
            $this->_getSession()->addError($e->getMessage());
        }
        catch(Exception $e){
            $this->_getSession()->addError($e->getMessage());
        }
        $this->getRequest()->setParam('invoice_id',null);
        $this->getRequest()->setParam('order_id',null);
        $this->_redirect('*/instorepickupadmin_order/detail',array('_current'=>true));
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

    protected  function _redirect($path, $arguments=array())
    {
        $path = '*/instorepickupadmin_order/detail';
        if(!empty($arguments['order_id'])){
            $arguments['id'] = $arguments['order_id'];
        }
        return parent::_redirect($path, $arguments);
    }

    protected function getCurrentUser()
    {
        return Mage::getSingleton('admin/session')->getUser();
    }

    protected function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Auto create invoice base on shipment
     * @param array $shipmentData
     * @return bool
     */
    protected function _autoCreateInvoice($shipmentData){
        if($this->getOrder()){
            if(!$this->getOrder()->canInvoice()){
                return;
            }
        }
        $data = array('items');
        $items = $this->getOrder()->getItemsCollection();
        $itemArr = array();
        foreach($items as $item){
            $itemArr[$item->getId()] = array(
                'qty_ordered' => $item->getQtyOrdered()*1,
                'qty_invoiced' => $item->getQtyInvoiced()*1,
                'qty_refuned' => $item->getQtyRefuned()*1,
                'qty_shipped' =>$item->getQtyShipped()*1
            );
        }
        //print_r($itemArr);die;
        $needCreateInvoice = false;
        foreach($shipmentData['items'] as $orderItemId => $shipmentItem){
            if(!empty($itemArr[$orderItemId])){
                $qtyNeedInvoice = ($itemArr[$orderItemId]['qty_shipped'] + $shipmentItem) - $itemArr[$orderItemId]['qty_invoiced'] ;
                if($qtyNeedInvoice > 0){
                    $data['items'][$orderItemId] = $qtyNeedInvoice;
                    $needCreateInvoice = true;
                }
            }
        }
        if($needCreateInvoice){
            $invoice = $this->_initInvoice($data);
            $isCaptureAllow = Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/capture');
            if($isCaptureAllow){
                if($invoice->canCapture()){
                    $data['capture_case'] = 'online';
                }
                elseif($invoice->getOrder()->getPayment()->getMethodInstance()->isGateway()){
                    $data['capture_case'] = 'offline';
                }
            }

            if ($invoice) {

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;

                $transactionSave->save();

                if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
                } elseif (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the invoice email.'));
                }
            }
        }
    }
}