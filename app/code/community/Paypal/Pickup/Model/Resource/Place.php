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
class Paypal_Pickup_Model_Resource_Place extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('paypal_pickup/place', 'place_id');
    }

    /**
     * Processing object after save data
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Paypal_Pickup_Model_Place
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $placeId = (int) $object->getData('place_id');
        $oldStores = $this->lookupStoreIds($placeId);
        $newStores = (array)$object->getData('stores');
        if (empty($newStores)) {
            $newStores = (array)$object->getData('store_id');
        }
        $table = $this->getTable('paypal_pickup/place_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        $isDeleted = (int) $object->getData('is_deleted');
        if ($delete && !$isDeleted) {
            $where = array(
                'place_id = ?' => $placeId,
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'place_id' => $placeId,
                    'store_id' => (int)$storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $placeId
     * @return array
     */
    public function lookupStoreIds($placeId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getTable('paypal_pickup/place_store'), 'store_id')
            ->where('place_id = ?', (int)$placeId);

        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Paypal_Pickup_Model_Resource_Place
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);

        }

        return parent::_afterLoad($object);
    }
}