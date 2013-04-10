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
class Paypal_Locationhere_Model_Locationhere_Delete
{
    public function delete_location_paypal_here($place_id){
        $model = Mage::getModel('paypal_locationhere/locationhere');
        $location_assignment = $model->getCollection()
                ->addFieldToFilter('place_id', intval($place_id))
                ->getFirstItem();
        /*
         * get paypal here location id
         */
        $paypal_here_location_id = $location_assignment->getPaypalHereLocationId();

        if (!is_null($paypal_here_location_id)) {
            /*
             * get website id of the deleted location
             */
            $store_id = Mage::getModel('paypal_pickup/place')
                          ->load($place_id)
                          ->getPaypalHereStoreView();
            $website_id = Mage::getModel('core/store')->load($store_id)->getWebsiteId();
            /*
             * send delete request to delete the record from paypal here api
             */
            $response = Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('/' . $paypal_here_location_id, null, Zend_Http_Client::DELETE, $website_id);
            /*
             * delete the assignment record from database
             */
            if (!Mage::helper('paypal_checkin')->responseHasErrors($response)) {
                $model->load($location_assignment->getAssignmentId())->delete();
                Mage::getModel('paypal_pickup/place')->load($place_id)->setPaypalHereApiEnabled(0)->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('paypal_pickup')->__('PayPal Here Location was successfully deleted'));                
            }
            else{
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('PayPal Here Location was not deleted. Error code: '. $response['errorCode'] . ' ' . $response['message']));
            }
        }
    }
}