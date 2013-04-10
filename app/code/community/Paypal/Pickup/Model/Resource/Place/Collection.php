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
class Paypal_Pickup_Model_Resource_Place_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize resources
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('paypal_pickup/place');
    }

    /**
     * Add collection filters by current store
     *
     * @return Paypal_Pickup_Model_Resource_Place_Collection
     */
    public function addStoreFilter()
    {
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('paypal_pickup/place')->getCollection();
        $collection->getSelect()->join(
            array('ps' => 'paypal_pickup_place_store'),
            'main_table.place_id = ps.place_id',
            array(
                'store_id' => 'store_id'
            ))
            ->where('store_id = ? or store_id = \'0\'', $storeId)
            ->group('main_table.place_id')
            ->distinct(true);

        return $collection;
    }
}