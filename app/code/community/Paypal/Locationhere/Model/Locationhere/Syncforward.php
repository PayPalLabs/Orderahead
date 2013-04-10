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
class Paypal_Locationhere_Model_Locationhere_Syncforward{
    //sync magento locations with paypal here locations                
    public function synchronize_location_paypal_here($observer) {     
        $params = $observer->getEvent()->getData();
        
        $place_model = $params['place_model'];
        
        if(is_null($place_model->getPlaceId()))
            $place_model->save();
        
        $place_id = $place_model->getPlaceId();
        
        $website_id = Mage::getModel('core/store')->load($place_model->getPaypalHereStoreView())->getWebsiteId();
        
        $location_record = Mage::getModel('paypal_locationhere/locationhere')
                            ->getCollection()
                            ->addFieldToFilter('place_id', $place_id)
                            ->getFirstItem();        
        //if API is turned on
        if ($place_model->getPaypalHereApiEnabled()) {
            // get location details from posting pararms
            $location_details = $this->getLocationDetails($place_model);
            $location_details = Mage::helper('core')->jsonEncode($location_details);
            $paypal_here_location_id = $location_record->getPaypalHereLocationId();
            
            //if the location is already existed in PayPal Here API then update it
            if (!is_null($paypal_here_location_id)) {
                $this->update_location($location_details, $paypal_here_location_id, $place_model, $website_id);
            } else { //if the location does not exist in PayPal Here API then create it
                $this->create_location($location_details, $place_id, $website_id);
            }
        } else { //if API is turned off
            Mage::getModel('paypal_locationhere/locationhere_delete')->delete_location_paypal_here($place_id);
        }          
    }
    
    protected function create_location($params, $place_id, $website_id) {
        $response = Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('', $params, Zend_Http_Client::POST, $website_id);
        if (!Mage::helper('paypal_checkin')->responseHasErrors($response)) {
            //add logo to new created location
            $logo = Mage::getStoreConfig('design/header/logo_src');
            Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('/'.$response['id'].'/logo', $logo, Zend_Http_Client::POST, $website_id);
            //assign magento location id to paypal here location id
            $checkin_here_assignment = Mage::getModel('paypal_locationhere/locationhere');
            $checkin_here_assignment->setPlaceId($place_id)
                                    ->setPaypalHereLocationId($response['id'])
                                    ->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('paypal_pickup')->__('PayPal Here Location was successfully created.'));
        } else{
            Mage::getModel('paypal_pickup/place')->load($place_id)->setPaypalHereApiEnabled(0)->save();
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('PayPal Here Location was not created. Error code: '. $response['errorCode'] . ' ' . $response['message']));            
        }
    }
    
    protected function update_location($params, $paypal_here_location_id, $place_model, $website_id) {    
        $place_id = $place_model->getPlaceId();
        //compare location data in magento and location data in paypal here api
        $paypal_here_location = Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('/' . $paypal_here_location_id, null, Zend_Http_Client::GET, $website_id);
        if($this->compareUrlAndCode($paypal_here_location, $place_id)){
            if($this->compareDetails($paypal_here_location, $place_id)){
                $response = Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('/' . $paypal_here_location_id, $params, Zend_Http_Client::PUT, $website_id);
                if (!(Mage::helper('paypal_checkin')->responseHasErrors($response))) {            
                    //update store location logo
                    //$logo = Mage::getStoreConfig('design/header/logo_src');
                    //Mage::getModel('paypal_locationhere/locationhere_api')->httpLocationRequest('/' . $paypal_here_location_id . '/logo', $logo, Zend_Http_Client::POST, $website_id);
                    $place_model->setLastAvailability(0);
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('paypal_pickup')->__('PayPal Here Location was successfully updated.'));
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('PayPal Here Location was not updated. Error code: '. $response['errorCode'] . ' ' . $response['message']));
                }
            } else {                
                $place_model->unsetData();
                $place_model->load($place_id);
                Mage::getModel('paypal_locationhere/locationhere_syncback')->setLocationDetails($place_model, $paypal_here_location);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Your place information has been replaced with your PayPal Here Location data. Please fill the information again and save to edit.'));                                                                
            }
        } else {
            Mage::getModel('paypal_locationhere/locationhere_delete')->delete_location_paypal_here($place_id);
            $place_model->setPaypalHereApiEnabled(0);
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('paypal_pickup')->__('Your URL or Place Code do not match the Tab Extension URL or Internal Name of the PayPal Here Location. You can no longer edit the location.'));
        }
    }    
    
    protected function getLocationDetails($place_model) {
        $location_details = array(
            "name" => $place_model->getPlaceName(),
            "internalName" => $place_model->getPlaceCode(),
            "mobility" => "fixed",
            "address" => array(
                "line1" => $place_model->getAddress(),
                "city" => $place_model->getCity(),
                "state" => $place_model->getRegionId()?Mage::getModel('directory/region')->load($place_model->getRegionId())->getName():'',
                "postalCode" => $place_model->getPostcode(),
                "country" => $place_model->getCountry()
            ),
            "phoneNumber" => $place_model->getPhone(),
            "latitude" => floatval($place_model->getLatitude()),
            "longitude" => floatval($place_model->getLongitude()),
            "tabType" => "standard",
            "availability" => "closed",
            "tabExtensionType" => "postOpen",
            "tabExtensionUrl" => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . Mage::getModel('core/store')->load($place_model->getPaypalHereStoreView())->getCode()
                                        . '/paypal_checkin/index/initcheckin?locationId={locationId}&customerId={customerId}&tabId={tabId}',
            //"tabExtensionUrl" => 'https://www.ezmshop.com/jj/paypal_checkin/index/initcheckin?locationId={locationId}&customerId={customerId}&tabId={tabId}',
        );
        return $location_details;
    }
    
    public function compareDetails($paypal_here_location, $place_id){
        $place = Mage::getModel('paypal_pickup/place')->load($place_id);
        $compare = ($paypal_here_location['name'] == $place->getPlaceName())                    
                    && ($paypal_here_location['address']['line1'] == $place->getAddress())
                    && ($paypal_here_location['address']['city'] == $place->getCity())
                    && ($paypal_here_location['address']['postalCode'] == $place->getPostcode())
                    && ($paypal_here_location['address']['country'] == $place->getCountry())
                    && ($paypal_here_location['phoneNumber'] == $place->getPhone())
                    && ($paypal_here_location['latitude'] == $place->getLatitude())
                    && ($paypal_here_location['longitude'] == $place->getLongitude());
        return $compare;
    }
    
    public function compareUrlAndCode($paypal_here_location, $place_id){
        $place = Mage::getModel('paypal_pickup/place')->load($place_id);
        $baseUrl = explode('/paypal_checkin/', $paypal_here_location['tabExtensionUrl']);
        $baseUrl = $baseUrl[0];
        $urlMatch = (trim($baseUrl) == trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . Mage::getModel('core/store')->load($place->getPaypalHereStoreView())->getCode()));
        //$urlMatch = (trim($baseUrl) == trim("https://www.ezmshop.com/jj"));
        $codeMatch = (trim($paypal_here_location['internalName']) == trim($place->getPlaceCode()));
        return ($urlMatch && $codeMatch);
    }
}   