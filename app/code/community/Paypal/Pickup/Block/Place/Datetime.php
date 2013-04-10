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
class Paypal_Pickup_Block_Place_Datetime extends Mage_Core_Block_Template
{
    protected $_dateHelper;
    /**
     * Place
     *
     * @var Paypal_Pickup_Model_Place $place
     */
    protected $_place;

    /**
     * Datetime format
     */
    const DATE_TIME_FORMAT = 'dd/MM/yyyy';

    protected function _construct()
    {
        parent::_construct();
        $this->_dateHelper = Mage::helper('paypal_pickup');

    }

    /**
     * Get place
     *
     * @return Paypal_Pickup_Model_Place
     */
    public function getPlace()
    {
        if (is_null($this->_place)) {
            if ($placeId = $this->getPlaceId()) {
                $this->_place = Mage::getModel('paypal_pickup/place')->load($placeId);
            }
        }
        return $this->_place;
    }

    /**
     * Check pickup type is specify datetime
     *
     * @return boolean
     */
    public function isPickupSpecifyDatetime()
    {
        $place = $this->getPlace();
        return (int)$place->getType() == Paypal_Pickup_Model_Place::PICKUP_TYPE_SPECIFY_DATETIME;
    }

    /**
     * Get Json config for datetime render
     *
     * @return Json
     */
    public function getJsonConfig()
    {
        $config = $this->getAllowedDays();
        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Get available date/time
     *
     * @return array
     */
    public function getAllowedDays()
    {
        /* @var $place Paypal_Pickup_Model_Place */
        $place = $this->getPlace();
        $finalDateTime = array();
        $currentDatetime = $this->_dateHelper->getCurrentDatetime($place->getTimezone());

        if ($this->isPickupSpecifyDatetime()) {
            $minDay = $place->getDatetimeMinDays();
            $maxDay = $place->getDatetimeMaxDays();
            if ($minDay < $maxDay) {
                $openDatetime = $place->getOpenDatetime();
                $storeHourExceptions = $place->getStoreHourExceptions();
                $openDays = $this->getOpenDays($openDatetime);
                $enableTimeSelector = $place->getDatetimeEnableTimeSelector();
                $period = (int)$place->getDatetimeMinutesDisplayed();

                for ($d = $minDay; $d <= $maxDay; $d++) {
                    $tmpDateTime = $this->_dateHelper->getCurrentDatetime($place->getTimezone())->addDay($d) ;
                    $tmpDate = $tmpDateTime->toString(Paypal_Pickup_Block_Place_Datetime::DATE_TIME_FORMAT);
                    $tmpDay = $tmpDateTime->get(Zend_Date::WEEKDAY_DIGIT);
                    if (in_array($tmpDay, $openDays)) {
                        $data = $openDatetime[$tmpDay];
                        for ($i = $data['from_hour']; $i <= $data['to_hour']; $i++) {
                            if ($enableTimeSelector) {
                                if($currentDatetime->get(Zend_Date::DAY) == $tmpDateTime->get(Zend_Date::DAY)){

                                    if($i >= $currentDatetime->get(Zend_Date::HOUR)){
                                        $currentTime = array(
                                            'minute'=>$currentDatetime->get(Zend_Date::MINUTE),
                                            'hour' => $currentDatetime->get(Zend_Date::HOUR),
                                        );
                                        $availableMinutes = $this->getAvailableMinutes($data['from_hour'], $data['from_minute'], $data['to_hour'], $data['to_minute'], $period, $i,$currentTime);

                                        if(!empty($availableMinutes)){
                                            $finalDateTime[$tmpDate][$i] = $availableMinutes;
                                        }
                                    }
                                }
                                else{
                                    $finalDateTime[$tmpDate][$i] = $this->getAvailableMinutes($data['from_hour'], $data['from_minute'], $data['to_hour'], $data['to_minute'], $period, $i);
                                }
                            } else {
                                $finalDateTime[$tmpDate] = $tmpDate;
                            }
                        }
                    }

                    // Check store hour exceptions
                    $flag = true;
                    $closeDays = array();
                    foreach ($storeHourExceptions as $inc => $exDataArr) {
                        $exDateTime = $this->_dateHelper->getDateFromString($exDataArr['day'],$place->getTimezone());
                        $exDate = $exDateTime->toString(Paypal_Pickup_Block_Place_Datetime::DATE_TIME_FORMAT);
                        $exIsOpen = $exDataArr['is_open'];
                        if(!$exIsOpen){
                            $closeDays[$exDate] = $exDate;
                        }
                        $exFromHour = $exDataArr['from_hour'];
                        $exFromMinute = $exDataArr['from_minute'];
                        $exToHour = $exDataArr['to_hour'];
                        $exToMinute = $exDataArr['to_minute'];
                        // Get exception day
                        if ($exDate) {
                            $exRealDay = $exDateTime->get(Zend_Date::WEEKDAY_DIGIT);
                            if ($exRealDay && $tmpDate == $exDate) {
                                if (!in_array($exDate,$closeDays)) {
                                    if (!in_array($exRealDay, $openDays)) {
                                        for ($m = $exFromHour; $m <= $exToHour; $m++) {
                                            if ($enableTimeSelector) {
                                                $availableMinutes= $this->getAvailableMinutes($exFromHour, $exFromMinute, $exToHour, $exToMinute, $period, $m);
                                                if(!empty($availableMinutes)){
                                                    if(is_array($finalDateTime[$tmpDate][$m])){
                                                        $finalDateTime[$tmpDate][$m] = array_unique(array_merge($finalDateTime[$tmpDate][$m],$availableMinutes));
                                                    }
                                                    else{
                                                        $finalDateTime[$tmpDate][$m] = $availableMinutes;
                                                    }

                                                }
                                            } else {
                                                $finalDateTime[$tmpDate] = $tmpDate;
                                            }
                                        }
                                    } else {
                                        if ($enableTimeSelector) {
                                            // Unset normal open hour
                                            if ($flag) {
                                                unset($finalDateTime[$tmpDate]);
                                                $flag = false;
                                            }
                                            for ($l = $exFromHour; $l <= $exToHour; $l++) {
                                                $finalDateTime[$tmpDate][$l] = $this->getAvailableMinutes($exFromHour, $exFromMinute, $exToHour, $exToMinute, $period, $l);
                                            }
                                        } else {
                                            $finalDateTime[$tmpDate] = $tmpDate;
                                        }
                                    }
                                } else {
                                    // Close the day
                                    unset($finalDateTime[$tmpDate]);
                                }
                            }
                        }
                    }
                    // remove day if not exits hour
                    if(empty($finalDateTime[$tmpDate])){
                        unset($finalDateTime[$tmpDate]);
                    }
                }
            }
        }

        return $finalDateTime;
    }

    /**
     * Get open days
     *
     * @param array $datetime
     * @return array
     */
    public function getOpenDays($datetime)
    {
        $openDays = array();
        foreach ($datetime as $day => $data) {
            if ($data['is_open'] == 1) {
                $openDays[] = $day;
            }
        }

        return $openDays;
    }

    /**
     * Get available minutes of hour
     *
     * @param int $fromHour
     * @param int $fromMinute
     * @param int $toHour
     * @param int $toMinute
     * @param int $period
     * @param int $var
     * @return array
     */
    public function getAvailableMinutes($fromHour, $fromMinute, $toHour, $toMinute, $period, $var,$currentTime=null)
    {
        $minutes = array();
        if ($var < $toHour) {
            if ($var == $fromHour) {
                $from = $fromMinute;
                $to = 60;
            } else {
                $from = 0;
                $to = 60;
            }
        } else {
            $from = 0;
            $to = $toMinute + 1;
        }
        for ($j = 0; $from + $j * $period < $to; $j++) {
            $tmpMinute = $from + $j * $period;
            if (!in_array($tmpMinute, $minutes)) {

                if(empty($currentTime)){
                    $minutes[] = $tmpMinute;
                }
                else{
                    if($var > $currentTime['hour']){
                        $minutes[] = $tmpMinute;
                    }
                    elseif($var = $currentTime['hour']){
                        if($tmpMinute >= $currentTime['minute']){
                            $minutes[] = $tmpMinute;
                        }
                    }
                }

            }
        }
        return $minutes;
    }

    /**
     * Get allowed durations options
     *
     * @return array
     */
    public function getAllowedDurationsOptions()
    {
        $place = $this->getPlace();
        $durations = explode(',', $place->getDurationsOptions());
        $allowedDurations = array();
        $openDatetime = $place->getOpenDatetime();
        $storeHourExceptions = $place->getStoreHourExceptions();
        $openDays = $this->getOpenDays($openDatetime);
        $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $i = 0;
        foreach ($durations as $period) {
            $i++;
            $tmpDatetime = $this->_dateHelper->getCurrentDatetime($place->getTimezone())->addMinute($period);
            $tmpDate = $tmpDatetime->toString(Paypal_Pickup_Block_Place_Datetime::DATE_TIME_FORMAT);
            $tmpDay = strtolower($tmpDatetime->get(Zend_Date::WEEKDAY_DIGIT));
            // Get allow periods
            if (in_array($tmpDay, $openDays)) {
                if($this->isAllowDuration($tmpDatetime,$openDatetime[$tmpDay])){
                    $allowedDurations[$i] = $period;
                }
            }
            $closeDays = array();
            foreach ($storeHourExceptions as $inc => $exDataArr) {
                $exDay = $exDataArr['day'];
                $exDate = $this->_dateHelper->getDateFromString($exDay,$place->getTimezone())->toString(Paypal_Pickup_Block_Place_Datetime::DATE_TIME_FORMAT);
                $exIsOpen = $exDataArr['is_open'];
                if(!$exIsOpen){
                    $closeDays[$exDate] = $exDate;
                }
                // Get datetime when the exception starts
                $fromDatetime = $this->_dateHelper->getDateFromString($exDay,$place->getTimezone())
                    ->setHour($exDataArr['from_hour'])
                    ->setMinute($exDataArr['from_minute']);
                // Get datetime when the exception ends
                $toDatetime = $this->_dateHelper->getDateFromString($exDay,$place->getTimezone())
                    ->setHour($exDataArr['to_hour'])
                    ->setMinute($exDataArr['to_minute']);
                // Update exception periods
                if ($exDate) {

                    $exRealDay = $this->_dateHelper->getDateFromString($exDataArr['day'],$place->getTimezone())->get(Zend_Date::WEEKDAY_DIGIT);
                    if ($exRealDay) {
                        if ($tmpDate == $exDate) {
                            if (!in_array($exDate,$closeDays)) {
                                // Check current datetime with start/end datetime
                                if ($tmpDatetime->getTimestamp() > $toDatetime->getTimestamp() || $tmpDatetime->getTimestamp() < $fromDatetime->getTimestamp()) {
                                    if (in_array($tmpDay, $openDays)) {
                                        unset($allowedDurations[$i]);
                                    }
                                } else {
                                    $allowedDurations[$i] = $period;
                                }
                            } else {
                                // Close the day
                                unset($allowedDurations[$i]);
                            }
                        }
                    }
                }
            }
        }

        return $allowedDurations;
    }

    /**
     * Get available minutes of hour
     *
     * @param int $fromHour
     * @param int $fromMinute
     * @param int $toHour
     * @param int $toMinute
     * @param int $period
     * @param int $var
     * @return array
     */
    public function isAllowDuration($datetime,$openDay)
    {
        $result = false;
        if(!empty($openDay['is_open'])){
            // Get datetime when the exception starts
            $fromDatetime = clone($datetime);
            $fromDatetime->setHour($openDay['from_hour'])->setMinute($openDay['from_minute']);
            // Get datetime when the exception ends
            $toDatetime = clone($datetime);
            $toDatetime->setHour($openDay['to_hour'])->setMinute($openDay['to_minute']);
            if($datetime->getTimestamp() <= $toDatetime->getTimestamp() && $datetime->getTimestamp() >= $fromDatetime->getTimestamp()){
                $result = true;
            }
        }
        return $result;
    }
}