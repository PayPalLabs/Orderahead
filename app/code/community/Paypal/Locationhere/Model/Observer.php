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
class Paypal_Locationhere_Model_Observer
{
    
    public function synchronize_location($observer){
        try{
            Mage::getModel('paypal_locationhere/locationhere_syncforward')->synchronize_location_paypal_here($observer);
        } catch(Exception $e){
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());   
        }
    }        
    
    public function delete_location($observer){
        try{
            $params = $observer->getEvent()->getData();
            $location_id = $params['id'];
            Mage::getModel('paypal_locationhere/locationhere_delete')->delete_location_paypal_here($location_id);
        } catch(Exception $e){
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());   
        }
    }
    
    public function checkLocationStatus(){
        try{
            Mage::log('===============> Check Location Status');
            Mage::getModel('paypal_locationhere/locationhere_openstores')->check_location_status_paypal_here();
        } catch(Exception $e){
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());   
        }
    }        
    
    public function synchronizeBackLocations(){
        try{
            Mage::log('===============> Sync back');
            Mage::getModel('paypal_locationhere/locationhere_syncback')->get_paypal_here_locations();            
        } catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
}   