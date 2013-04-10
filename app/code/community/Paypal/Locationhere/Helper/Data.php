<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Locationhere
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
class Paypal_Locationhere_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getOpenDateTime(){
        $paypal_here_location_id = Mage::getSingleton('core/session')->getCheckinLocationId();
        
        $place_id = Mage::getModel('paypal_locationhere/locationhere')
                    ->getCollection()
                    ->addFieldToFilter('paypal_here_location_id', $paypal_here_location_id)
                    ->getFirstItem()->getPlaceId();
        
        $place = Mage::getModel('paypal_pickup/place')->load($place_id);
        
        if(!is_null($place)){
                $open_datetime = array(
                  'exception' => $place->getStoreHourExceptions(),
                  'weekday_open' => $place->getOpenDatetime(),
                  'mindate' => $place->getDatetimeMinDays(),
                  'maxdate' => $place->getDatetimeMaxDays(),
                  'enabled' => $place->getAllowPickupDatetime(),
                  'timeEnabled' => $place->getDatetimeEnableTimeSelector(),
                  'minuteStep' => $place->getDatetimeMinutesDisplayed()
                );
                return $open_datetime;
        }

        return null;
    }
}