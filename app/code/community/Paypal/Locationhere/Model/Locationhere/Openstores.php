<?php
/**
 * Pickup
 *
 * @package      :  Paypal_LocationHere
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
?>
<?php
class Paypal_Locationhere_Model_Locationhere_Openstores
{
    public function check_location_status_paypal_here() {
        $paypal_here_location_collection = Mage::getModel('paypal_locationhere/locationhere')
                                            ->getCollection();        
        foreach ($paypal_here_location_collection as $paypal_here_location) {            
            $paypal_here_location_id = $paypal_here_location->getPaypalHereLocationId();
            $place = Mage::getModel('paypal_pickup/place')
                    ->getCollection()
                    ->addFieldToFilter('place_id', $paypal_here_location->getPlaceId())
                    ->getFirstItem();
            $current_time = $this->getTime($place->getTimeZone());        
            $store_hour_exceptions = $place->getStoreHourExceptions();
            $open = 0;
            $close = 0;
            foreach ($store_hour_exceptions as $exception) {
                $exception_date = strtotime($exception['day']);
                if ($exception_date == $current_time['day']) {
                    if (intval($exception['is_open'])) {
                        $from_in_minute = (int) $exception['from_hour'] * 60 + (int) $exception['from_minute'];
                        $to_in_minute = (int) $exception['to_hour'] * 60 + (int) $exception['to_minute'];
                        if ($from_in_minute <= $current_time['current_time'] && $current_time['current_time'] <= $to_in_minute) {                            
                            $open = 1;
                        } else {
                            $close = 1;
                        }
                    } else {
                        Mage::log('Close by exception');
                        $this->set_location_availability($paypal_here_location_id, $place, 0);
                        return;
                    }
                }                
            }
            if($open){
                Mage::log('Open by exception');
                $this->set_location_availability($paypal_here_location_id, $place, 1);
                return;
            } else if ($close){
                Mage::log('Close by exception');
                $this->set_location_availability($paypal_here_location_id, $place, 0);
                return;
            }
            $open_date_time = $place->getOpenDatetime();
            $open_date_time = $open_date_time[$current_time['weekday']];
            if ((int) $open_date_time['is_open']) {
                $from_in_minute = (int) $open_date_time['from_hour'] * 60 + (int) $open_date_time['from_minute'];
                $to_in_minute = (int) $open_date_time['to_hour'] * 60 + (int) $open_date_time['to_minute'];
                if ($from_in_minute <= $current_time['current_time'] && $current_time['current_time'] <= $to_in_minute) {
                    $this->set_location_availability($paypal_here_location_id, $place, 1);
                } else {
                    $this->set_location_availability($paypal_here_location_id, $place, 0);
                }
            } else {
                $this->set_location_availability($paypal_here_location_id, $place, 0);
            }
        }
    }
    
    protected function set_location_availability($location_id, $place, $availability) {
        $last_availability = $place->getLastAvailability();
        if($availability != $last_availability){
            $website_id = Mage::getModel('core/store')->load(intval($place->getPaypalHereStoreView()))->getWebsiteId();
            /*
            * get paypal here location detail
            */
            $paypal_here_location = Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('/' . $location_id, null, Zend_Http_Client::GET, $website_id);
            if (Mage::getModel('paypal_locationhere/locationhere_syncforward')->compareUrlAndCode($paypal_here_location, $place->getPlaceId())) {
                if(Mage::getModel('paypal_locationhere/locationhere_syncforward')->compareDetails($paypal_here_location, $place->getPlaceId())){
                    $params = array(
                        "name" => $paypal_here_location['name'],
                        "internalName" => $paypal_here_location['internalName'],
                        "mobility" => $paypal_here_location['mobility'],
                        "address" => array(
                            "line1" => $paypal_here_location['address']['line1'],
                            "city" => $paypal_here_location['address']['city'],
                            "state" => $paypal_here_location['address']['state'],
                            "postalCode" => $paypal_here_location['address']['postalCode'],
                            "country" => $paypal_here_location['address']['country']
                        ),
                        "phoneNumber" => $paypal_here_location['phoneNumber'],
                        "latitude" => $paypal_here_location['latitude'],
                        "longitude" => $paypal_here_location['longitude'],
                        "tabType" => $paypal_here_location['tabType'],
                        "availability" => $availability ? 'open' : 'closed',
                        "tabExtensionType" => $paypal_here_location['tabExtensionType'],
                        "tabExtensionUrl" => $paypal_here_location['tabExtensionUrl'],
                    );
                    $location_details = Mage::helper('core')->jsonEncode($params);
                    $website_id = Mage::getModel('core/store')->load($place->getPaypalHereStoreView())->getWebsiteId();
                    $response = Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('/' . $location_id, $location_details, Zend_Http_Client::PUT, $website_id);
                    $place->setLastAvailability($availability);
                    $place->save();
                    Mage::log((($availability)?'Open ':'Close ').$location_id);
                } else {
                    Mage::log('Your store information does not match with the data from PayPal Here Location ' . $location_id);
                }
            } else {
                Mage::log('Your URL or Place Code does not match with the Tab Extension Url or Internal Name of the location ' . $location_id);
            }
        }
    }
    
     /*
     * return weekday and current time
     */

    protected function getTime($timezone) {
        date_default_timezone_set($timezone);
        $timestamp = time();
        $hour = (int) date('H', $timestamp);
        $minute = (int) date('i', $timestamp);
        $dw = date('w', $timestamp);

        $current_time = $hour * 60 + $minute;

        $array = array(
            'current_time' => $current_time,
            'weekday' => $dw,
            'day' => strtotime(date('Y-m-d'))
        );
        return $array;
    }
    
}