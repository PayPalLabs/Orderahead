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
class Paypal_Pickup_PlaceController extends Mage_Core_Controller_Front_Action
{
    /**
     * Ajax action
     */
    public function ajaxAction()
    {
        if ($placeId = $this->getRequest()->getParam('id')) {
            $placeModel = Mage::getModel('paypal_pickup/place');
            $html = $this->getLayout()->createBlock('paypal_pickup/place_datetime')->setPlaceId($placeId)->setTemplate('paypal/pickup/place/datetime.phtml')->toHtml();
            $result = array('html' => $html);
            echo json_encode($result);
            exit;
        }
    }

    /**
     * Ajax action in paypal review
     */
    public function reviewAjaxAction()
    {
        if ($placeCode = $this->getRequest()->getParam('place_code')) {
            $place = Mage::getModel('paypal_pickup/place')->getCollection()
                ->addFieldToFilter('place_code', $placeCode)
                ->getFirstItem();
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $html = '';
            $placeDate = '';
            $placeHour = '';
            $placeMinute = '';
            $placeDuration = '';
            $pickupTime = $quote['pickup_time'];
            $format = 'dd/MM/yyyy';
            if ($place) {
                $placeId = $place->getPlaceId();
                if (!empty($pickupTime)) {
                    $datetime = $pickupTime;
                    $tmpTime = strtotime(Mage::app()->getLocale()->date($datetime, null, null, false));
                    if ($place->getType() == Paypal_Pickup_Model_Place::PICKUP_TYPE_SPECIFY_DATETIME) {
                        $placeDate = Mage::app()->getLocale()->date($datetime, null, null, false)->toString($format);
                        if (strpos($datetime, ':')) {
                            $placeHM = explode(':', substr($datetime, strlen($placeDate) + 1));
                            $placeHour = (int)$placeHM[0];
                            $placeMinute = (int)$placeHM[1];
                        }
                    } else {
                        $durationsOptions = explode(',', $place->getDurationsOptions());
                        $placeDuration = $durationsOptions[0];
                        $tmp = 0;
                        foreach ($durationsOptions as $period) {
                            $periodTime = strtotime(Mage::app()->getLocale()->date()->addMinute($period));
                            $diff = abs($tmpTime-$periodTime);
                            if ($tmp > $diff) {
                                $placeDuration = $period;
                            }
                            $tmp = $diff;
                        }
                    }
                }
                $html = $this->getLayout()->createBlock('paypal_pickup/place_datetime')->setPlaceId($placeId)->setTemplate('paypal/pickup/place/datetime.phtml')->toHtml();
            }

            $result = array('html' => $html, 'place_id' => $placeId, 'type' => $place->getType(), 'place_date' => $placeDate, 'place_hour' => $placeHour, 'place_minute' => $placeMinute, 'place_durations_options' => $placeDuration);
            echo json_encode($result);
            exit;
        }
    }

    public function saveInstoreAjaxAction()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            $placeId = $data['place_id'];
            if ($placeId != '') {
                $pickupTime = '';
                $pickupLabel = '';
                $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if ($data['place_date'] != '') {
                    $pickupTime .= $data['place_date'];
                    if ($data['place_hour'] != '' && $data['place_minute'] != '') {
                        $pickupTime .= ' ' . $data['place_hour'] . ':' . $data['place_minute'];
                    }
                } else {
                    if ($data['place_durations_options'] != '') {
                        $durations = (int)$data['place_durations_options'];
                        $realTime = Mage::app()->getLocale()->date()->addMinute($durations)->toString('dd/MM/yyyy H:m');
                        $pickupTime .= $realTime;
                    }
                }
                if ($pickupTime != '') {
                    $pickupLabel .= Mage::helper('core')->formatDate($pickupTime, $format, true);
                }
                $quoteData = Mage::getSingleton('checkout/session')->getQuote();
                $quoteId = $quoteData['entity_id'];
                if ($quoteId) {
                    $quote = Mage::getModel('sales/quote')->load($quoteId);
                    $quote->setPickupTime($pickupLabel);
                    try {
                        $quote->save();
                    } catch (Exception $e) {
                        $session = Mage::getSingleton("checkout/session");
                        $session->addError($e->getMessage());
                    }
                }
            }
        }
        return;
    }
}