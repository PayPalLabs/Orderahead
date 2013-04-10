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
class Paypal_Locationhere_Model_Locationhere_Syncback
{
    public function get_paypal_here_locations(){
        /*
         * get magento website id lists
         */
        $website_collection = Mage::getModel('core/website')->getCollection();        
        foreach($website_collection as $website){
            $website_id = $website->getWebsiteId();            
            if(Mage::helper('paypal_checkin')->getIsenabled($website_id)){                                
                //get locations of the merchant on PayPal Here API
                $locations = Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest(null, null, Zend_Http_Client::GET, $website_id);
                $locations = $locations['locations'];

                if(count($locations)){
                    //get paypal here location lists
                    $paypal_here_location_ids = array();
                    foreach($locations as $location){
                        $paypal_here_location_ids[] = $location['id'];
                    }

                    //get place lists                                 
                    $places_collection = Mage::getModel('paypal_locationhere/locationhere')->getCollection();
                    $local_location_ids = array();
                    foreach($places_collection as $place){                        
                        $local_location_id = $place->getPaypalHereLocationId();
                        $store_view_id = Mage::getModel('paypal_pickup/place')
                                        ->load($place->getPlaceId())
                                        ->getPaypalHereStoreView();
                        if(Mage::getModel('core/store')->load($store_view_id)->getWebsiteId() == $website_id){
                            $local_location_ids[] = $local_location_id;
                        }
                    }
                    
                    //list of locations to be deleted 
                    $locations_for_deletion_ids = array_diff($local_location_ids, $paypal_here_location_ids);
                    foreach($locations_for_deletion_ids as $location_id){
                        $assignment = Mage::getModel('paypal_locationhere/locationhere')
                                ->getCollection()
                                ->addFieldToFilter('paypal_here_location_id', $location_id)
                                ->getFirstItem();
                        Mage::getModel('paypal_pickup/place')
                                ->load($assignment->getPlaceId())
                                ->setPaypalHereApiEnabled(0);
                        $assignment->delete();
                    }

                    //list of locations to be created
                    $locations_for_creation_ids = array_diff($paypal_here_location_ids, $local_location_ids);
                    foreach($locations_for_creation_ids as $location_id){
                        $internalName = $this->find_internal_name($locations, $location_id);

                        $place_id = Mage::getModel('paypal_pickup/place')
                                 ->getCollection()
                                 ->addFieldToFilter('place_code', $internalName)
                                 ->getFirstItem()
                                 ->getPlaceId();

                        if(!$place_id){
                            $place_id = $this->create_new_pickup_place($this->find_details($locations, $location_id), $website_id);                                                        
                        }                          
                    }

                    //list of locations to be updated
                    $locations_for_update_ids = array_intersect($paypal_here_location_ids, $local_location_ids);  
                    foreach($locations_for_update_ids as $location_id){
                        $assignment = Mage::getModel('paypal_locationhere/locationhere')
                                ->getCollection()
                                ->addFieldToFilter('paypal_here_location_id', $location_id)
                                ->getFirstItem();
                        $this->update_pickup_place($this->find_details($locations, $location_id), $assignment->getPlaceId(), $website_id);
                    }        
                }
            }
        }        
    }        
    
    protected function create_new_pickup_place($location, $website_id){
        $model = Mage::getSingleton('paypal_pickup/place');
        $this->setLocationUrl($model, $location, $website_id, 1);
    }
    
    protected function update_pickup_place($location, $place_id, $website_id){
        $place = Mage::getSingleton('paypal_pickup/place')->load($place_id);
        $this->setLocationUrl($place, $location, $website_id, 0);
    }
    
    protected function setLocationUrl($place, $location, $website_id, $new){                
        if(array_key_exists('tabExtensionUrl', $location)){
            $extension_base_url = explode('/paypal_checkin/', $location['tabExtensionUrl']);
            $extension_base_url = trim($extension_base_url[0]);
            if (strpos($extension_base_url, Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)) !== false) {
                $store_code = explode('https://www.ezmshop.com', $extension_base_url);
                $store_code = explode('/', $store_code[1]);
                $store_code = (trim($store_code[1]) !== '')?trim(($store_code[1])):'default';                
                $store_id = Mage::getModel('core/store')->getCollection()
                            ->addFieldToFilter('website_id', $website_id)
                            ->addFieldToFilter('code', $store_code)
                            ->getFirstItem()
                            ->getStoreId();
                if ($store_id) {
                    $this->setLocationDetails($place, $location, $store_id, $new);
                }
            }
        }            
    }
    
    public function setLocationDetails($place, $location, $store_id, $new){  
        Mage::log(($new?'Create ':'Update ') . 'location ' . $location['id']);
        $place->setPlaceCode($location['internalName'])
              ->setPlaceName($location['name'])
              ->setStatus(($location['status'] == 'active')?1:0);
        if(array_key_exists('address', $location)){
            $address = $location['address'];
            if(array_key_exists('line1', $address)) $place->setAddress($address['line1']);            
            if(array_key_exists('city',$address)) $place->setCity($address['city']);
            if(array_key_exists('country', $address)) $place->setCountry($address['country']);
            if(array_key_exists('state', $address)) $place->setRegionId(array_key_exists('state', $location['address'])?Mage::getModel('directory/region')->loadByCode($location['address']['state'], $location['address']['country'])->getRegionId():'');
            if(array_key_exists('postalCode', $address)) $place->setPostcode($address['postalCode']);
        } 
        if(array_key_exists('phoneNumber', $location)) $place->setPhone($location['phoneNumber']);
        if(array_key_exists('latitude', $location) || array_key_exists('longitude', $location)){
            $place->setLatitude($location['latitude'])
                  ->setLongitude($location['longitude']);
        }        
        if(array_key_exists('email', $location)) $place->setEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $place->setPaypalHereApiEnabled(1)
              ->setLastAvailability(($location['availability'] == 'open')?1:0)
              ->setPaypalHereStoreView(intval($store_id))
              ->save();        
        //create a new place
        if($new){
            $place_id = $place->getPlaceId();
            $place->unsetData();
            Mage::getModel('paypal_pickup/place_store')->setPlaceId($place_id)
                    ->setStoreId($store_id)
                    ->save()->unsetData();
            Mage::getModel('paypal_locationhere/locationhere')->setPlaceId($place_id)
                    ->setPaypalHereLocationId($location['id'])
                    ->save()->unsetData();
        }
    }
    
    protected function find_internal_name($locations, $id){
        foreach($locations as $location){
            if($location['id'] == $id){
                return $location['internalName'];
            }
        }
        return null;
    }
    
    protected function find_details($locations, $id){
        foreach($locations as $location){
            if($location['id'] == $id)
                return $location;
        }
        return null;
    }
}