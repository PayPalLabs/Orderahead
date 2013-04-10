<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Pickup
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
class Paypal_Pickup_Model_Observer
{
    /**
     * Save instore information
     *
     * @param   Varien_Event_Observer $observer
     * @return  Paypal_Pickup_Model_Observer
     */
    public function saveInstoreInformation(Varien_Event_Observer $observer)
    {
        $data = $observer->getRequest()->getParams();

        if ($data) {
            $placeId = $data['place-id'];
            $place = Mage::getModel('paypal_pickup/place')->load($placeId);
            if ($placeId != '') {
                $pickupTime = '';
                if ($data['place-date'] != '') {
                    $pickupTime .= $data['place-date'];
                    if ($data['place-hour'] != '' && $data['place-minute'] != '') {
                        $pickupTime .= ' ' . $data['place-hour'] . ':' . $data['place-minute'];
                    }
                } else {
                    if ($data['place-durations-options'] != '') {
                        $durations = (int)$data['place-durations-options'];
                        $pickupTime .= Mage::helper('paypal_pickup')->getCurrentDatetime($place->getTimezone())->addMinute($durations)->toString('dd/MM/yyyy H:m');
                    }
                }

                $quote = $observer->getQuote();
                $quote->setPickupTime($pickupTime);

                try {
                    $quote->save();
                } catch (Exception $e) {
                    $session = Mage::getSingleton("checkout/session");
                    $session->addError($e->getMessage());
                }
            }
        }

        return;
    }

    /**
     * Add pickup time into shipping description
     *
     * @param   Varien_Event_Observer $observer
     * @return  Paypal_Pickup_Model_Observer
     */
    public function updateShippingDescription(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();
        $shippingDescription = $order->getShippingDescription();
        $pickupTime = $order->getPickupTime();
        $placeId = $order->getPlaceId();
        if (!empty($pickupTime)) {
            // Format datetime by locale
            $pickupTimeLabel = Mage::helper('paypal_pickup')->__('Pickup time: ');
            //$format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
            $timezone = Mage::app()->getStore()->getConfig('general/locale/timezone');
            if($placeId){
                $place= Mage::getModel('paypal_pickup/place')->load($placeId);
                if($place->getId()){
                    $timezone = $place->getTimezone();
                }
            }
            $pickupTimeLabel .= $pickupTime.' ('.$timezone.')';
            $shippingDescription .= '<span class="pickup-time">' . $pickupTimeLabel . '</span>';
        }
        $order->setShippingDescription($shippingDescription);
        $order->save();

        return;
    }

    /**
     * Replace shipping address by place address in case shipping method is pickup instore
     *
     * @param   Varien_Event_Observer $observer
     * @return  Paypal_Pickup_Model_Observer
     */
    public function updateShippingAddress(Varien_Event_Observer $observer)
    {

        $order = $observer->getOrder();
        $shippingMethod = $order->getShippingMethod();
        if (strpos($shippingMethod, 'instore') !== false) {
            $placeCode = substr($shippingMethod, 8);
            /* @var $place Paypal_Pickup_Model_Place */
            $place = Mage::getModel('paypal_pickup/place')->getCollection()
                ->addFieldToFilter('place_code', $placeCode)
                ->getFirstItem();

            /* @var $shippingAddress Mage_Sales_Model_Order_Address */
            $shippingAddress = $order->getShippingAddress();

            if ($place) {
                // Save place information
                $shippingAddress->setStreetFull($place->getAddress());
                $shippingAddress->setCity($place->getCity());
                $shippingAddress->setRegion($place->getRegion());
                $shippingAddress->setRegionId($place->getRegionId());
                $shippingAddress->setPostcode($place->getPostcode());
                $shippingAddress->setCountryId($place->getCountry());
                $shippingAddress->save();
                // Save place id
                $order->setPlaceId($place->getPlaceId())->save();
            }
        }

        return;
    }
}