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
class Paypal_Pickup_Model_Shipping_Carrier_Instore extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Instore shipping method code
     *
     * @var string
     */
    protected $_code = 'instore';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request){
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $places = Mage::getResourceModel('paypal_pickup/place_collection')->addStoreFilter()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('is_deleted', 0);
        $result = Mage::getModel('shipping/rate_result');
        foreach ($places as $place) {
            // Prepare rate information
            $handling = Mage::getStoreConfig('carriers/' . $this->_code . '/handling_fee');
            $shippingPrice = '0.00';
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier('instore');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($place->getPlaceCode());
            $method->setMethodTitle($place->getPlaceName());
            $method->setPrice($shippingPrice + $handling);
            $method->setCost($shippingPrice);

            // Add method for place
            $result->append($method);
        }

        return $result;
    }

    public function getAllowedMethods()
    {
        return array('instore' => $this->getConfigData('name'));
    }
}