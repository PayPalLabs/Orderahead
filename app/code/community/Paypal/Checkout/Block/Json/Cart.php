<?php
/**
 * PayPal Checkout
 *
 * @package      :  PayPal_Checkout
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */
class Paypal_Checkout_Block_Json_Cart extends Mage_Checkout_Block_Cart
{
    protected function formatPrice( $price ) {
        $formatted = Mage::helper('core')->currency($price, true, false);
        return preg_replace('/[^0-9\.]/', '', $formatted);
    }

    public function _toHtml() {
        $array = array();
        $array['items'] = array();
        $helper = Mage::helper('catalog/product_configuration');
        $cart = Mage::getSingleton('checkout/cart');

        foreach($this->getItems() as $item) {
            $product = $item->getProduct();
            $option = $item->getOptionByCode('product_type');
            if ($option) {
                $product = $option->getProduct();
            }
            $options = $helper->getOptions($item);
            $itemOptions = array();
            
            foreach($options as $option) {
                $itemOptions[] = array(
                    'label' => $option['label'],
                    'value' => (array_key_exists("print_value",$option))? $option['print_value']:$option['value'],
                    );
            }

            $img = Mage::helper('paypal_catalog')->getProductMedia($product->getId(), false);
            if ($img && count($img) > 0) {
                $img = reset($img);
                $img = $img['url'];
            }
            else $img = '';

            $array['currency_code'] = Mage::app()->getStore()->getCurrentCurrencyCode();
            $array['items'][] = array(
                'url' => Mage::app()->getStore()->getBaseUrl().'/jsoncatalog/product/viewtemplate/id/'.$product->getId(),
                'id' => $item->getId(),
                'product' => $product->getName(),
                'img' => $img,
                'quantity' => $item->getQty() * 1,
                'minqty' => $product->getStockItem()->getMinSaleQty(),
                'maxqty' => $product->getStockItem()->getMaxSaleQty(),
                'price' => number_format($item->getCalculationPrice(), 2, '.', ''),//$this->formatPrice($item->getCalculationPrice()),
                'options' => $itemOptions,
                );
        }

        $totals = $cart->getQuote()->getTotals();
        foreach($totals as $key => $total) {
            $value = $total->getData();
            if (isset($value['value']))
                $array['total'][$key] = number_format($value['value'], 2, '.', '');//$this->formatPrice($value['value']);
            else $array['total'][$key] = '0.00';
        }

        if (!isset($array['total']['tax']) || $array['total']['tax'] == '') {
            $array['total']['tax'] = '0.00';
        }
        if (!isset($array['total']['discount']) || $array['total']['discount'] == '') {
            $array['total']['discount'] = '0.00';
        }
        
        $quote = $this->getQuote();        
        $shippingAddress = $quote->getShippingAddress();                               
        $array['total']['shipping'] = number_format($shippingAddress->getShippingAmount(), 2, '.', ''); //$this->formatPrice($shippingAddress->getShippingAmount());

        $array['pickup_time'] = date('d M Y h:i A', Mage::getModel('core/date')->timestamp(time()));
        $checkinLocationDetails = Mage::getSingleton('core/session')->getCheckinLocationDetails();
        $array['status'] = $checkinLocationDetails['status'];
        $array['availability'] = $checkinLocationDetails['availability'];

        $array['static_block'] = Mage::helper("paypal_checkout")->getStaticBlock();       
        $array['shipping_title'] = Mage::getSingleton('sales/quote_address_rate')->getCollection()
                                                               ->addFieldToFilter('address_id', $shippingAddress->getAddressId())
                                                               ->getFirstItem()
                                                               ->getCarrierTitle();
        $array['locale'] = Mage::app()->getLocale()->getDefaultLocale();
        $array['open_datetime'] = Mage::helper('paypal_locationhere')->getOpenDateTime();
        return Mage::helper('core')->jsonEncode($array);
    }
}
