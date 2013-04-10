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
class Paypal_Locationhere_Model_Locationhere_Api {
    /*
     * paypal here api url
     */
    protected $_uri = "https://www.paypal.com/webapps/hereapi/merchant/v1/locations";
    
    public function httpLocationRequest($endpoint = '', $params, $method = 'GET', $website_id = '') {
        try {
            
            $response = "";

            $endpoint = $this->_uri . $endpoint;
            
            $iClient = new Zend_Http_Client();
            
            $iClient->setUri($endpoint)
                    ->setMethod($method)
                    ->setConfig(array(
                        'maxredirects' => 0,
                        'timeout' => 30,
                    ));
            Mage::getSingleton('core/session')->setCheckinAuthWebsite($website_id);
            $accessToken = Mage::getModel('paypal_checkin/api_auth')->callTokenserviceRefresh($website_id);
            
            $headers = array(
                "Authorization" => 'Bearer ' . $accessToken,
                "Content-Type" => "application/json"
            );
            
            $iClient->setHeaders($headers);

            if ($method == 'GET') {
                $iClient->setParameterGet($params);
            } else {
                $iClient->setRawData($params);
            }

            $responseJson = $iClient->request();        
            
            $response = Mage::helper('core')->jsonDecode($responseJson->getBody());
        } catch (Zend_Http_Client_Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        return $response;
    }
}