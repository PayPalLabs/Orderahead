<?php
/**
 * Pickup
 *
 * @method int getId()
 * @method Paypal_Pickup_Model_Place_Store setId(int $value)
 * @method int getPlaceId()
 * @method Paypal_Pickup_Model_Place_Store setPlaceId(int $value)
 * @method int getStoreId()
 * @method Paypal_Pickup_Model_Place_Store setStoreId(int $value)
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
class Paypal_Pickup_Model_Place_Store extends Mage_Core_Model_Abstract
{
    /**
     * Initialize model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('paypal_pickup/place_store');
    }
}