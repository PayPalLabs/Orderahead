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
class Paypal_Pickup_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Minutes period config path
     */
    const PAYPAL_PICKUP_MINUTES_PERIOD = 'paypal_pickup/general/period';

    /**
     * Get place by place code
     *
     * @param string $placeCode
     * @return Paypal_Pickup_Model_Place
     */
    public function getPlace($placeCode)
    {
        $place = Mage::getResourceModel('paypal_pickup/place_collection')
            ->addStoreFilter()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('place_code', $placeCode)
            ->getFirstItem();

        return $place;
    }

    /**
     * Retrieve Hour Options
     *
     * @return array
     */
    public function getHourOptionArray()
    {
        $data = array();
        for ($i = 0; $i < 24; $i++) {
            $data[] = array(
                'label' => $i,
                'value' => $i,
            );
        }

        return array_values($data);
    }

    /**
     * Retrieve Minutes Options
     *
     * @return array
     */
    public function getMinuteOptionArray()
    {
        $period = $this->getMinutesPeriod();
        $data = array();
        for ($i = 0; $i * $period < 60; $i++) {
            $optionLabel = $i * $period;
            if ($i == 0) {
                $optionLabel = '00';
            }
            $data[] = array(
                'label' => $optionLabel,
                'value' => $i * $period,
            );
        }

        return array_values($data);
    }

    /**
     * Retrieve Minutes Period
     *
     * @return int
     */
    public function getMinutesPeriod()
    {
        $period = Mage::getStoreConfig(self::PAYPAL_PICKUP_MINUTES_PERIOD);
        if (!preg_replace("/[^0-9]/", "", $period) || !is_numeric($period) || ((int)$period < 0) || ((int)$period > 60)) {
            $period = 15;
        } else {
            $period = (int)$period;
        }
        return $period;
    }

    /**
     * Retrieve datetime from datetime string
     *
     * @return Zend_Date
     */
    public function getDateFromString($strDate,$timezone=null)
    {
        $datetimeArr = explode('/',$strDate);
        $mon = $datetimeArr[0];
        $day = $datetimeArr[1];
        $year = $datetimeArr[2];
        $date = new Zend_Date();
        $date->setTimezone($timezone)->setDay($day)->setMonth($mon)->setYear($year);
        return $date;
    }

    /**
     * Retrieve datetime from datetime string
     *
     * @return Zend_Date
     */
    public function getCurrentDatetime($timezone=null)
    {
        $date = new Zend_Date();
        $date->setTimezone($timezone);
        return $date;
    }
}