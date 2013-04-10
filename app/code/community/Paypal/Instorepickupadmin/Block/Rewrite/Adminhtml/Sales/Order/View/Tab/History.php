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
class Paypal_Instorepickupadmin_Block_Rewrite_Adminhtml_Sales_Order_View_Tab_History extends Mage_Adminhtml_Block_Sales_Order_View_Tab_History
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/instorepickupadmin/sales/order/view/tab/history.phtml');
    }

    protected function _prepareHistoryItem($label, $notified, $created, $comment = '',$username='',$isSecure=false)
    {
        return array(
            'title'      => $label,
            'notified'   => $notified,
            'comment'    => $comment,
            'created_at' => $created,
            'username' => $username,
            'is_security' => $isSecure
        );
    }

    /**
     * Compose and get order full history.
     * Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     * @return array
     */
    public function getFullHistory()
    {
        $order = $this->getOrder();

        $history = array();
        foreach ($order->getAllStatusHistory() as $orderComment){
            $createdAt = strtotime($orderComment->getCreatedAt()) . '_' . $orderComment->getId();
            $history[$createdAt] = $this->_prepareHistoryItem(
                $orderComment->getStatusLabel(),
                $orderComment->getIsCustomerNotified(),
                $orderComment->getCreatedAtDate(),
                $orderComment->getComment(),
                $orderComment->getUsername(),
                $orderComment->getIsSecurity()
            );
        }
        foreach ($order->getCreditmemosCollection() as $_memo){
            $createdAt = strtotime($_memo->getCreatedAt()) . '_' . $_memo->getId();
            $history[$createdAt] =
                $this->_prepareHistoryItem($this->__('Credit memo #%s created', $_memo->getIncrementId()),
                    $_memo->getEmailSent(), $_memo->getCreatedAtDate());

            foreach ($_memo->getCommentsCollection() as $_comment){
                $createdAt = strtotime($_comment->getCreatedAt()). '_' .$_comment->getId();
                $history[$createdAt] =
                    $this->_prepareHistoryItem($this->__('Credit memo #%s comment added', $_memo->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAtDate(), $_comment->getComment(),$_comment->getUsername(),$_comment->getIsSecurity());

            }

        }

        foreach ($order->getShipmentsCollection() as $_shipment){
            $createdAt = strtotime($_shipment->getCreatedAt()).'_'.$_shipment->getId();
            $history[$createdAt] =
                $this->_prepareHistoryItem($this->__('Shipment #%s created', $_shipment->getIncrementId()),
                    $_shipment->getEmailSent(), $_shipment->getCreatedAtDate());

            foreach ($_shipment->getCommentsCollection() as $_comment){
                $createdAt = strtotime($_comment->getCreatedAt()). '_' . $_comment->getId();
                $history[$createdAt] =
                    $this->_prepareHistoryItem($this->__('Shipment #%s comment added', $_shipment->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAtDate(), $_comment->getComment(),$_comment->getUsername(),$_comment->getIsSecurity());

            }

        }

        foreach ($order->getInvoiceCollection() as $_invoice){
            $createdAt = strtotime($_invoice->getCreatedAt()).'_'.$_invoice->getId();
            $history[$createdAt] =
                $this->_prepareHistoryItem($this->__('Invoice #%s created', $_invoice->getIncrementId()),
                    $_invoice->getEmailSent(), $_invoice->getCreatedAtDate());

            foreach ($_invoice->getCommentsCollection() as $_comment){
                $createdAt = strtotime($_comment->getCreatedAt()). '_' .$_comment->getId();
                $history[$createdAt] =
                    $this->_prepareHistoryItem($this->__('Invoice #%s comment added', $_invoice->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAtDate(), $_comment->getComment(),$_comment->getUsername(),$_comment->getIsSecurity());

            }

        }
        foreach ($order->getTracksCollection() as $_track){
            $createdAt = strtotime($_track->getCreatedAt()).'_'.$_track->getId();
            $history[$createdAt] =
                $this->_prepareHistoryItem($this->__('Tracking number %s for %s assigned', $_track->getNumber(), $_track->getTitle()),
                    false, $_track->getCreatedAtDate());
        }

        krsort($history);
        return $history;
    }
}
