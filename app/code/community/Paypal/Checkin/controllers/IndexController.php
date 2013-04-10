<?php
/**
 * PayPal Checkin
 *
 * @package      :  PayPal_Checkin
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 */
class Paypal_Checkin_IndexController extends Mage_Core_Controller_Front_Action {

    protected $_checkout = null;
    protected $_config = null;
    protected $_quote = false;
    protected $_configType = 'paypal/config';
    protected $_checkoutType = 'paypal_checkin';

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function initcheckinAction() {

        //Get querystring parameters and store them in customer session
        $params = $this->getRequest()->getParams();

        $session = Mage::getSingleton('core/session');

        if (array_key_exists('locationId', $params)) {
            $session->setCheckinLocationId($params['locationId']);
        };
        
        if (array_key_exists('customerId', $params)){
            $session->setCheckinCustomerId($params['customerId']);
        };
        
        if (array_key_exists('tabId', $params)) {
        	if(strpos($params['tabId'], "?") !== false){	
        	 	$params['tabId'] = (explode("?", $params['tabId']));
        	 	$session->setCheckinTabId($params['tabId'][0]);
        	}else{
            	$session->setCheckinTabId($params['tabId']);
        	}
		}

        if (array_key_exists('uri', $params)) {
            $this->_redirect($params['uri']);
        } else {
            $this->_redirect('/');
        }
    }

    public function checkoutAction() {
        $date = new DateTime(); // For generating unique sid
        $order = null;
        try {
            $this->_initCheckout();
            $quote = $this->_getQuote();
            $quote->setIsMultiShipping(false);
            //$quote->removeAllAddresses();
            // Paypal API init
            $locationId = Mage::getSingleton('core/session')->getCheckinLocationId();
            $tabId = Mage::getSingleton('core/session')->getCheckinTabId();
            
            // get PayPal customer info
            $customer = $quote->getCustomer();
            
            $quote->setCustomerId(null)
                    ->setCustomerIsGuest(true)
                    ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
            
            $api_data = Mage::getModel('paypal_checkin/api_here');
            $customerData = $api_data->callTab($locationId, $tabId);
            if(isset($customerData['customerName'])){
            	$customer->setFirstname($customerData['customerName']);
            }
            

            $quote->setCustomer($customer);
            
            $api_data = Mage::getModel('paypal_checkin/api_auth');
            $merchantData = $api_data->callUserinfo();
            $merchantData = array_key_exists('identity', $merchantData) ? $merchantData['identity']:"";
            $merchantEmail = Mage::helper("paypal_checkin")->getMerchantEmail(Mage::app()->getStore()->getWebsiteId());
            if (isset($merchantEmail)) {
            	//$customer->setEmail($customerData['emails'][0]);
            	//Set the customerData in session to use it for callInvoices
            	Mage::getSingleton('core/session')->setCheckinMerchantEmail($merchantEmail);
            };
            
            // get PayPal customer billing address info
            $billingAddress = Mage::getModel('sales/quote_address');
            
            //if (isset($merchantData['firstName'])) {
            if(isset($customerData['customerName'])){
                $billingAddress->setFirstname($customerData['customerName']);
            };
            if (isset($merchantData['middleName'])) {
                $billingAddress->setMiddlename($merchantData['middleName']);
            };
            if (isset($merchantData['lastName'])) {
                $billingAddress->setLastname($merchantData['lastName']);
            };
            if (isset($merchantData['address']['locality'])) {
                $billingAddress->setCity($merchantData['address']['locality']);
            };
            if (isset($merchantData['address']['region'])) {
                $billingAddress->setRegion($merchantData['address']['region']);
            };
            if (isset($merchantData['address']['postal_code'])) {
                $billingAddress->setPostcode($merchantData['address']['postal_code']);
            };
            if (isset($merchantData['address']['country'])) {
                $billingAddress->setCountryId($merchantData['address']['country']);
            };
            if (isset($merchantData['address']['telephone'])) {
                $billingAddress->setTelephone($merchantData['address']['telephone']);
            };
            if (isset($merchantData['address']['street_address'])) {
                $billingAddress->setStreet($merchantData['address']['street_address']);
            };

            $billingAddress->setShouldIgnoreValidation(true);
            $quote->setBillingAddress($billingAddress);
            
            // get store location
            // Paypal API init
            $api_data = Mage::getModel('paypal_checkin/api_here');
            $locations_details = $api_data->callLocations($locationId, $tabId);
            $location_details = isset($locations_details['locations']) ? 
            					$locations_details['locations'][0] : $locations_details;
            
            //Set location details in session to be used in invoices
            Mage::getSingleton('core/session')->setCheckinLocationDetails($location_details);
            
            $shippingAddress = Mage::getModel('sales/quote_address');

            if (isset($merchantData['firstName'])) {
                $shippingAddress->setFirstname($merchantData['firstName']);
            };
            if (isset($merchantData['middleName'])) {
                $shippingAddress->setMiddlename($customerData['middleName']);
            };
            if (isset($merchantData['lastName'])) {
                $shippingAddress->setLastname($merchantData['lastName']);
            };
            if (isset($location_details['name'])) {
                $shippingAddress->setCompany($location_details['name']);
            };

            if (isset($location_details['address']['city'])) {
                $shippingAddress->setCity($location_details['address']['city']);
            };
            if (isset($location_details['address']['state'])) {
                $shippingAddress->setRegion($location_details['address']['state']);
            };
            if (isset($location_details['address']['line1'])) {
                $shippingAddress->setStreet($location_details['address']['line1']);
            };
            if (isset($location_details['address']['postalCode'])) {
                $shippingAddress->setPostcode($location_details['address']['postalCode']);
            };
            if (isset($location_details['address']['country'])) {
                $shippingAddress->setCountryId($location_details['address']['country']);
            };
            if (isset($location_details['phoneNumber'])) {
                $shippingAddress->setTelephone($location_details['phoneNumber']);
            };

            $quote->setShippingAddress($shippingAddress);

            $quote->getShippingAddress()->setShouldIgnoreValidation(true);
            //get website id
            $websiteId = Mage::app()->getStore()->getWebsiteId();
            $quote->getShippingAddress()->setShippingMethod(Mage::helper("paypal_checkin")->getShippingMethod($websiteId))->setCollectShippingRates(true);//->collectShippingRates();
            
            // Payment method

            $payment = $quote->getPayment();
            
            //methods: authorizenet, paypal_express, googlecheckout, purchaseorder
            $payment->setMethod('paypal_checkin');
            $quote->setPayment($payment);

            $quote->collectTotals()->save();

            // placeOrder
            $service = Mage::getModel('sales/service_quote', $quote);
            
            $service->submitAll();
            $quote->save();
            
            $order = $service->getOrder();
            if (!$order) {
                return;
            }
            
            // Call invoices
            $ppInvoiceId = $api_data->callInvoices(Mage::helper('paypal_checkin')->getOrderItems($order));
            $ppTransactionId = $api_data->callPay($ppInvoiceId);

            if( (isset($ppInvoiceId)) && (!(trim($ppInvoiceId) === '')) && (isset($ppTransactionId)) && (!(trim($ppTransactionId) === ''))){
	            switch ($order->getState()) {
	                // even after placement paypal can disallow to authorize/capture, but will wait until bank transfers money
	                case Mage_Sales_Model_Order::STATE_PENDING_PAYMENT:
	                case Mage_Sales_Model_Order::STATE_NEW:
	                    // TODO
	                    break;
	                // regular placement, when everything is ok
	                case Mage_Sales_Model_Order::STATE_PROCESSING:
	                case Mage_Sales_Model_Order::STATE_COMPLETE:
	                case Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW:
	                    $order->sendNewOrderEmail();
	                    break;
	            }
	            
	            //Create invoice with pp invoice id and transaction id in comments
	            Mage::helper('paypal_checkin')->createInvoice($order, $ppInvoiceId, $ppTransactionId);

                //set pickup time
                $params = $this->getRequest()->getParams();
                if (isset($params['pickup_time']) && $params['pickup_time'] != '') {
                    $order->setPickupTime($params['pickup_time']);
                }
	            
	            //change order status according to order state
	            //$order->setStatus(Paypal_Checkin_Helper_Data::ORDER_STATUS_CODE_PAID);
	            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Paypal_Checkin_Helper_Data::ORDER_STATUS_CODE_PAID);
	            $order->save();
	            $order->sendNewOrderEmail(true);

	
	            $this->_order = $order;
	
	            // prepare session to success or cancellation page
	            $session = $this->_getCheckoutSession();
	            $session->clearHelperData();
	
	            // "last successful quote"
	            $quoteId = $this->_getQuote()->getId();
	            $session->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);
	
	            if ($order) {
	                $session->setLastOrderId($order->getId())
	                        ->setLastRealOrderId($order->getIncrementId());
	            }
            }else{
            	Mage::helper('paypal_checkin')->cancelOrder($order);
            	Mage::log("Cancelled order, no transaction id nor invoice id");
            }
            $this->_redirect('jsoncheckout/onepage/successtemplate/s/' . $date->format('U'));
            return;
        } catch (Zend_Http_Client_Exception $e){
        	Mage::logException($e);
        	if(!is_null($order)) Mage::helper('paypal_checkin')->cancelOrder($order);
            $this->_getCheckoutSession()->addError($this->__($e->getMessage()));
            $this->_redirect('jsoncheckout/cart/template/s/' . $date->format('U'));
        } 
        catch (Mage_Core_Exception $e) {
        	Mage::logException($e);
        	if(!is_null($order)) Mage::helper('paypal_checkin')->cancelOrder($order);
            $this->_getCheckoutSession()->addError($this->__($e->getMessage()));
            $this->_redirect('jsoncheckout/cart/template/s/' . $date->format('U'));
        } catch (Exception $e) {
        	Mage::logException($e);
        	if(!is_null($order)) Mage::helper('paypal_checkin')->cancelOrder($order);
            $this->_getCheckoutSession()->addError($this->__($e->getMessage()));
            $this->_redirect('jsoncheckout/cart/template/s/' . $date->format('U'));
        }
    }

    /**
     * Instantiate quote and checkout
     * @throws Mage_Core_Exception
     */
    private function _initCheckout() {
        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Checkin.'));
        }
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    private function _getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    private function _getCustomerSession(){
        return Mage::getSingleton('core/session');
    }
    
    private function _getCoreSession(){
        return Mage::getSingleton('core/session');
    }
    
    
    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    private function _getQuote() {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    /**
     *
     */
    public function addToCartAction() {
        
        try {
            $params = $this->getRequest()->getPost();
            
            $productId = $params["product"];
            $qty = $params["qty"];
            
            if($qty <= 0) Mage::throwException(Mage::helper('paypal_checkin')->__('Please choose at least one product'));
            
            //TODO: Add check for null or blank product id
            $product = Mage::getModel('catalog/product')->load($productId);

            $cart = Mage::getModel('checkout/cart');
            $cart->init();
            $cart->addProduct($product, $params);
            $cart->save();

            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

            $this->_redirect("*/*/checkout");
        }/**
         * if there are less than one product, not allow users to checkin 
         */ 
        catch (Exception $e){
            Mage::logException($e);
        	$this->_getCoreSession()->addError($this->__($e->getMessage()));
            $this->_redirect('catalog/product/view/id/'.$productId);
        }
    }
}
