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
require_once ('Mage'.DS.'Adminhtml'.DS. 'controllers' .DS. 'Sales' .DS .'Order'.DS.'CreditmemoController.php');
class Paypal_Instorepickupadmin_Adminhtml_Instorepickupadmin_CreditmemoController extends Mage_Adminhtml_Sales_Order_CreditmemoController
{
    /**
     * Save creditmemo
     * We can save only new creditmemo. Existing creditmemos are not editable
     */
    public function saveAction()
    {
        try{

            $orderId = $this->getRequest()->getParam('id');
            $this->getRequest()->setParam('order_id',$orderId);
            $order = $this->_initOrder();
            $postData = $this->getRequest()->getPost('creditmemo');
            $comment = !empty($postData['comment_text']) ? $postData['comment_text'] : '';

            if ($order) {
                if(!empty($comment)){
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
                if($order->getPayment()->canRefundPartialPerInvoice()){
                    // refund online base on invoice
                    $invoices = $order->getInvoiceCollection();

                    foreach($invoices as $invoice){
                        if ($this->_isAllowedAction('creditmemo') && $invoice->getOrder()->canCreditmemo()) {
                            $orderPayment = $invoice->getOrder()->getPayment();
                            if (($orderPayment->canRefundPartialPerInvoice()
                                && $invoice->canRefund()
                                && $orderPayment->getAmountPaid() > $orderPayment->getAmountRefunded())
                                || ($orderPayment->canRefund() && !$invoice->getIsUsedForRefund())) {

                                $this->getRequest()->setParam('invoice_id',$invoice->getId());
                                $creditmemo = $this->_initCreditmemo();
                                Mage::unregister('current_creditmemo');
                                $items = $creditmemo->getAllItems();
                                $isBackToStock = 1;
                                $qtys = array();
                                // refund all item invoiced
                                foreach ($items as $item) {
                                    $qtys[$item->getOrderItemId()] = array(
                                        'qty' => $item->getQty()*1,
                                        'back_to_stock' => $isBackToStock
                                    );
                                }

                                $data = array(
                                    "items" => $qtys,
                                    "do_offline" => "0",
                                    "comment_text" => $comment,
                                    "shipping_amount" => $this->getShippingAmount($creditmemo),
                                    "adjustment_positive" => $creditmemo->getBaseAdjustmentFeePositive()*1,
                                    "adjustment_negative" => $creditmemo->getBaseAdjustmentFeeNegative()*1,
                                );

                                if($creditmemo->getInvoice() && $creditmemo->getInvoice()->getTransactionId()){
                                    $data['do_offline'] = "0";
                                } else {
                                    $data['do_offline'] = "1";
                                }

                                $this->getRequest()->setPost('creditmemo',$data);
                                if ($creditmemo) {
                                    if (($creditmemo->getGrandTotal() <=0) && (!$creditmemo->getAllowZeroGrandTotal())) {
                                        Mage::throwException(
                                            $this->__('Credit memo\'s total must be positive.')
                                        );
                                    }

                                    if (isset($data['do_refund'])) {
                                        $creditmemo->setRefundRequested(true);
                                    }
                                    if (isset($data['do_offline'])) {
                                        $creditmemo->setOfflineRequested((bool)(int)$data['do_offline']);
                                    }

                                    $creditmemo->register();
                                    $this->_saveCreditmemo($creditmemo);
                                    $creditmemo->sendEmail(!empty($data['send_email']), $comment);
                                } else {
                                    $this->_getSession()->addError($this->__('Can\'t create credit memo.'));
                                }

                            }
                        }
                    }
                } else {
                    // refund offline
                    $creditmemo = $this->_initCreditmemo();
                    Mage::unregister('current_creditmemo');
                    $items = $creditmemo->getAllItems();
                    $isBackToStock = 1;
                    $qtys = array();
                    // refund all item invoiced
                    foreach ($items as $item) {
                        $qtys[$item->getOrderItemId()] = array(
                            'qty' => $item->getQty()*1,
                            'back_to_stock' => $isBackToStock
                        );
                    }
                    $comment = !empty($postData['comment_text']) ? $postData['comment_text'] : '';
                    $data = array(
                        "items" => $qtys,
                        "do_offline" => "1",
                        "comment_text" => $comment,
                        "shipping_amount" => $this->getShippingAmount($creditmemo),
                        "adjustment_positive" => $creditmemo->getBaseAdjustmentFeePositive()*1,
                        "adjustment_negative" => $creditmemo->getBaseAdjustmentFeeNegative()*1,
                    );
                    $this->getRequest()->setPost('creditmemo',$data);
                    parent::saveAction();
                }
            }
        }
        catch(Exception $e){
            $this->getRequest()->setParam('order_id',null);
            $this->_getSession()->addError($e->getMessage());
        }
        $this->getRequest()->setParam('invoice_id',null);
        $this->getRequest()->setParam('order_id',null);
        $this->_redirect('*/instorepickupadmin_order/detail',array('_current'=>true));
    }

    protected  function _redirect($path, $arguments=array())
    {
        $path = '*/instorepickupadmin_order/detail';
        if (!empty($arguments['order_id'])) {
            $arguments['id'] = $arguments['order_id'];
        }
        return parent::_redirect($path, $arguments);
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


    /**
     * Get credit memo shipping amount depend on configuration settings
     * @param Mage_Sales_Model_Order_Creditmemo
     * @return float
     */
    protected function getShippingAmount($creditmemo)
    {
        $config = Mage::getSingleton('tax/config');
        if ($config->displaySalesShippingInclTax($creditmemo->getOrder()->getStoreId())) {
            $shipping = $creditmemo->getBaseShippingInclTax();
        } else {
            $shipping = $creditmemo->getBaseShippingAmount();
        }
        return Mage::app()->getStore()->roundPrice($shipping) * 1;
    }

    protected function getCurrentUser(){
        return Mage::getSingleton('admin/session')->getUser();
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
     * Check whether is allowed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }
}