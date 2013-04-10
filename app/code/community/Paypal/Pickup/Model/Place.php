<?php
/**
 * Pickup
 *
 * @method int getPlaceId()
 * @method Paypal_Pickup_Model_Place setPlaceId(int $value)
 * @method string getPlaceCode()
 * @method Paypal_Pickup_Model_Place setPlaceCode(string $value)
 * @method string getPlaceName()
 * @method Paypal_Pickup_Model_Place setPlaceName(string $value)
 * @method int getStatus()
 * @method Paypal_Pickup_Model_Place setStatus(int $value)
 * @method string getDescription()
 * @method Paypal_Pickup_Model_Place setDescription(string $value)
 * @method array getOpenDatetime()
 * @method Paypal_Pickup_Model_Place setOpenDatetime(string $value)
 * @method string getAddress()
 * @method Paypal_Pickup_Model_Place setAddress(string $value)
 * @method int getCity()
 * @method Paypal_Pickup_Model_Place setCity(string $value)
 * @method int getRegion()
 * @method Paypal_Pickup_Model_Place setRegion(string $value)
 * @method int getRegionId()
 * @method Paypal_Pickup_Model_Place setRegionId(int $value)
 * @method string getPostcode()
 * @method Paypal_Pickup_Model_Place setPostcode(string $value)
 * @method string getCountry()
 * @method Paypal_Pickup_Model_Place setCountry(string $value)
 * @method string getLongitude()
 * @method Paypal_Pickup_Model_Place setLongitude(string $value)
 * @method string getLatitude()
 * @method Paypal_Pickup_Model_Place setLatitude(string $value)
 * @method string getPhone()
 * @method Paypal_Pickup_Model_Place setPhone(string $value)
 * @method string getEmail()
 * @method Paypal_Pickup_Model_Place setEmail(string $value)
 * @method int getAllowPickupDatetime()
 * @method Paypal_Pickup_Model_Place setAllowPickupDatetime(int $value)
 * @method Paypal_Pickup_Model_Place setStoreHourExceptions(string $value)
 * @method int getType()
 * @method Paypal_Pickup_Model_Place setType(int $value)
 * @method string getDurationsOptions()
 * @method Paypal_Pickup_Model_Place setDurationsOptions(string $value)
 * @method int getDatetimeMinDays()
 * @method Paypal_Pickup_Model_Place setDatetimeMinDays(int $value)
 * @method int getDatetimeMaxDays()
 * @method Paypal_Pickup_Model_Place setDatetimeMaxDays(int $value)
 * @method int getDatetimeEnableTimeSelector()
 * @method Paypal_Pickup_Model_Place setDatetimeEnableTimeSelector(int $value)
 * @method int getDatetimeMinutesDisplayed()
 * @method Paypal_Pickup_Model_Place setDatetimeMinutesDisplayed(int $value)
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
class Paypal_Pickup_Model_Place extends Mage_Core_Model_Abstract
{
    /**
     * Pickup type specify datetime
     */
    const PICKUP_TYPE_SPECIFY_DATETIME = 0;

    /**
     * Pickup type durations options
     */
    const PICKUP_TYPE_DURATIONS_OPTIONS = 1;

    /**
     * Store hour exception config
     *
     * @var array
     */
    protected $_storeHourExceptionConfig;

    protected $_timezone;

    /**
     * Initialize model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('paypal_pickup/place');
    }

    /**
     * Get open date time
     *
     * @return array
     */
    public function getOpenDatetime()
    {
        $openDatetime = $this->getData('open_datetime');
        if(is_string($openDatetime)){
            $openDatetime = unserialize($openDatetime);
        }
        return $openDatetime;
    }

    /**
     * Get store hour exceptions
     *
     * @return array
     */
    public function getStoreHourExceptions()
    {
        $storeHourEx = $this->getData('store_hour_exceptions');
        $exceptionsData = array();
        if(is_string($storeHourEx) && !empty($storeHourEx)){
            $tmpExceptions = unserialize($storeHourEx);
            if(!empty($tmpExceptions)){
                foreach ($tmpExceptions as $key => $tmpData) {
                    if ($key != '__empty' && !empty($tmpData['day'])) {
                        $exceptionsData[] = array(
                            'day' => $tmpData['day'],
                            'is_open' => $tmpData['is_open'],
                            'from_hour' => $tmpData['from_hour'],
                            'from_minute' => $tmpData['from_minute'],
                            'to_hour' => $tmpData['to_hour'],
                            'to_minute' => $tmpData['to_minute'],
                        );
                    }
                }
            }
        }
        return $exceptionsData;
    }

    /**
     * Check place is new
     *
     * @param string $placeCode
     * @return boolean
     */
    public function isNewPlace($placeCode)
    {
        $collection = $this->getCollection()->addFieldToFilter('place_code', $placeCode);
        if (sizeof($collection)) {
            return false;
        }

        return true;
    }

    /**
     * Get store hour exceptions config
     *
     * @return array
     */
    public function getStoreExceptionConfig()
    {
        if (is_null($this->_storeHourExceptionConfig)) {
            $storeHourExceptionConfig = $this->getData('store_hour_exceptions');
            if(is_string($storeHourExceptionConfig)){
                if (!empty($storeHourExceptionConfig)) {
                    $storeHourExceptionConfig = unserialize($storeHourExceptionConfig);
                    if(!empty($storeHourExceptionConfig)){
                        $storeHourExceptionConfig = array_filter($storeHourExceptionConfig);
                        $this->_storeHourExceptionConfig = $storeHourExceptionConfig;
                    }
                }
            }
        }
        return $this->_storeHourExceptionConfig;
    }

    public function getTimezone()
    {
        if(is_null($this->_timezone)){
            if($this->getData('timezone')){
                $this->_timezone = $this->getData('timezone');
            }
            else{
                $this->_timezone = Mage::app()->getStore()->getConfig('general/locale/timezone');
            }

        }
        return $this->_timezone;
    }
}