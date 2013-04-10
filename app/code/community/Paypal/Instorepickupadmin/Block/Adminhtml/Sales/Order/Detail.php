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
class Paypal_Instorepickupadmin_Block_Adminhtml_Sales_Order_Detail extends Paypal_Instorepickupadmin_Block_Adminhtml_Section
{

    public function __construct()
    {
        $this->setSectionId(Paypal_Instorepickupadmin_Helper_Data::SECTION_ORDER_DETAIL);
        $this->setSectionTitle($this->__('Order Detail'));

        $this->addConfirmPopup(
            array(
                'id' => 'refund_confirm',
                'name' => 'refund_confirm',
                'title' => $this->__('Confirm'),
                'message' => $this->__('Full amount will be refunded, even if the order is partially fulfilled.<br/>Would you like to proceed?'),
                'buttons' => array(
                    array(
                        'title' => $this->__('Cancel'),
                        'attributes' => array(
                                'id' => 'refund_confirm_cancel',
                                'data-rel' => 'back'
                            )
                    ),
                    array(
                        'title' => $this->__('Refund'),
                        'attributes' => array(
                            'id' => 'refund_confirm_refund',
                            'onclick' => '$(\'#order_refund\').submit()'
                        )
                    )
                )
            )
        );
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }

    /**
     * Check permission for allow add comment
     *
     * @return boolean
     */
    public function canAddComment()
    {
        return $this->_isAllowedAction('comment') &&
            $this->getOrder()->canComment();
    }

    /**
     * Check permission for allow add comment
     *
     * @return boolean
     */
    public function canCreditmemo()
    {
        return $this->_isAllowedAction('creditmemo') &&
            $this->getOrder()->canCreditmemo();
    }

    /**
     * Retrieve save action url
     *
     * @return string
     */
    public function getSaveAction()
    {
        $order = $this->getOrder();
        if($order->canShip() || $order->canInvoice()){
            return $this->getUrl('adminhtml/instorepickupadmin_shipment/save',array('_current' => true));
        }
        return $this->getUrl('adminhtml/instorepickupadmin_order/save',array('_current' => true));
    }

    /**
     * Retrieve refund action url
     *
     * @return string
     */
    public function getRefundAction()
    {
        $order = $this->getOrder();
        return $this->getUrl('adminhtml/instorepickupadmin_creditmemo/save',array('_current' => true));
    }

    public function getStatuses()
    {
        $state = $this->getOrder()->getState();
        $statuses = $this->getOrder()->getConfig()->getStateStatuses($state);
        return $statuses;
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }


}
