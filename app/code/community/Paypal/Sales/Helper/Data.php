<?php
/**
 * Sales
 *
 * @package      :  Paypal_Sales
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

class Paypal_Sales_Helper_Data extends Mage_Sales_Helper_Data
{
    public function simpleFormatPrice( $price ) {
        $formatted = str_replace(',', '.', Mage::helper('core')->currency($price, true, false));
        return preg_replace('/[^0-9\.]/', '', $formatted);
    }

    public function getOrderDetails($order) {
        $result['order'] = array(
            'id' => $order->getRealOrderId(),
            'total' => array(
                'grand_total' => number_format($order->getGrandTotal(), 2, '.', ''), //simpleFormatPrice($order->getGrandTotal()),
                'subtotal' => number_format($order->getSubtotal(), 2, '.', ''), //$this->simpleFormatPrice($order->getSubtotal()),
                'tax' => number_format($order->getTaxAmount(), 2, '.', ''), //$this->simpleFormatPrice($order->getTaxAmount()),
                'discount' => number_format($order->getDiscountAmount(), 2, '.', ''), //$this->simpleFormatPrice($order->getDiscountAmount()),
                'paid_amount' => number_format($order->getTotalPaid(), 2, '.', ''), //$this->simpleFormatPrice($order->getTotalPaid()),
                'shipping' => number_format($order->getShippingAmount(), 2, '.', ''), //$this->simpleFormatPrice($order->getShippingAmount()),
            ),
        );
        $result['status'] = $order->getStatusLabel();
        $result['currency_code'] = Mage::app()->getStore()->getCurrentCurrencyCode();
        $items = $order->getAllVisibleItems();

        foreach($items as $item) {
            $options = $item->getProductOptions();

            if (!isset($options['options'])) {
                if (isset($options['attributes_info'])) {
                    $selectedOptions = $options['attributes_info'];
                }
                else $selectedOptions = array();
            }
            else $selectedOptions = $options['options'];

            $itemOptions = array();
            foreach($selectedOptions as $option) {
                $itemOptions[] = array(
                    'label' => $option['label'],
                    'value' => $option['value'],
                    );
            }

            $result['order']['items'][] = array(
                'product' => $item->getName(),
                'id' => $item->getId(),
                'quantity' => (int)$item->getQtyOrdered(),//$options['info_buyRequest']['qty'] * 1,
                'price' => $this->simpleFormatPrice($item->getPrice()),
                'options' => $itemOptions,
                );
        }
        $result['order']['created_at'] = 
                Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', false)
                . ', '
                . Mage::helper('core')->formatTime($order->getCreatedAt(), 'medium', false);

        $shippingAddress = $order->getShippingAddress();//return shipping address
        //return  store address
        $storeStreet = '';
        $storeCity = '';
        if (!is_null($shippingAddress) && $shippingAddress != '') {
            $storeStreetArray = $shippingAddress->getStreet();
            foreach($storeStreetArray as $street)
                $storeStreet .= ' ' . $street;
            $storeCity = $shippingAddress->getCity();
        }
        
        $result['store'] = array(
            'name' => $order->getStore()->getName(),
            'img' => Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src')),
            'location' => trim($storeStreet . ' ' . $storeCity)
        );
        
        $result['shipping_title'] = $order->getShippingTitle();

        $billingAddress = NULL;
        if (is_null($order->getCustomerId())) { // Customer is guest
            $billingAddress = $order->getBillingAddress();
        }
        else { // Customer is logged in
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $billingAddress = $customer->getDefaultBillingAddress();
        }

        if (isset($billingAddress) && $billingAddress != '') {
            $result['buyername'] = trim($billingAddress->getFirstname()
                    . ($billingAddress->getMiddlename === null ? '' : ' ' . $billingAddress->getMiddlename())
                    . ' ' . $billingAddress->getLastname());
        }
        else $result['buyername'] = '';

        $result['pickup_time'] = '2/20/13 10:55 PM';
        
        return $result;
    }
}
