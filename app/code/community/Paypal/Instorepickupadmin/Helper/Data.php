<?php
/**
 * Instore Pickup Admin
 *
 * @package      :  Paypal_Instorepickupadmin
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
class Paypal_Instorepickupadmin_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SECTION_LOGIN = 'section-login';
    const SECTION_SEARCH_ORDER = 'section-search-order';
    const SECTION_SEARCH_ORDER_RESULT = 'section-search-order-result';
    const SECTION_ORDER_DETAIL = 'section-order-detail';
    const XML_PATH_ORDER_STATES = 'global/sales/order/states';
    const ORDER_COLOR_CODE_NEW_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/new';
    const ORDER_COLOR_CODE_PENDING_PAYMENT_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/pending_payment';
    const ORDER_COLOR_CODE_PROCESSING_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/processing';
    const ORDER_COLOR_CODE_COMPLETE_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/complete';
    const ORDER_COLOR_CODE_CLOSED_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/closed';
    const ORDER_COLOR_CODE_CANCELED_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/canceled';
    const ORDER_COLOR_CODE_HOLDED_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/holded';
    const ORDER_COLOR_CODE_PAYMENT_REVIEW_CONFIG_PATH = 'paypal_instorepickupadmin/order_color_code/payment_review';
    const USE_JQUERY_CDN_CONFIG_PATH = 'paypal_instorepickupadmin/general/use_jquery_cdn';

    /**
     * Get Default Section for application
     *
     * @return string
     */
    public function getDefaultSection()
    {
        $defaultSection = self::SECTION_LOGIN;
        $session = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn()) {
            $request = Mage::app()->getRequest();
            if ($request->getParam('section')) {
                $defaultSection = $request->getParam('section');
            } else {
                $defaultSection = self::SECTION_SEARCH_ORDER;
            }
        }
        return $defaultSection;
    }

    /**
     * Get Order Collection
     * @param string $state
     * @return string
     */
    public function getOrderStateColor($state)
    {
        $stateColor = '';
        $colors = array(
            'new' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_NEW_CONFIG_PATH),
            'pending_payment' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_PENDING_PAYMENT_CONFIG_PATH),
            'processing' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_PROCESSING_CONFIG_PATH),
            'complete' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_COMPLETE_CONFIG_PATH),
            'closed' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_CLOSED_CONFIG_PATH),
            'canceled' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_CANCELED_CONFIG_PATH),
            'holded' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_HOLDED_CONFIG_PATH),
            'payment_review' => Mage::getStoreConfig(self::ORDER_COLOR_CODE_PAYMENT_REVIEW_CONFIG_PATH)
        );
        if (!empty($colors[$state])) {
            $stateColor = $colors[$state];
        }
        return $stateColor;
    }

    /**
     * Get Order Collection
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */

    public function getOrderDetailHash($orderId)
    {
        $salt = 'fxgRTsvX25DFD';
        $hash = md5($salt . $orderId);
        return $hash;
    }

    /**
     * Get Order Collection
     *
     * @param string $code
     * @param string $orderId
     * @return boolean
     */

    public function checkSecurityCode($code, $orderId)
    {
        $hash = $this->getOrderDetailHash($orderId);
        if ($hash == $code) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve all order state
     *
     * @return array
     */
    public function getOrderStateLabel($order)
    {
        $statesConfig = Mage::getConfig()->getNode(self::XML_PATH_ORDER_STATES);

        $states = array();
        $state = '';
        foreach ($statesConfig->children() as $state => $node) {
            $states[$state] = $node->label;
        }
        if (!empty($states[$order->getState()])) {
            $state = $states[$order->getState()];
        }
        return $state;
    }

    public function getLogoUrl()
    {
        $path = Mage::getStoreConfig('paypal_instorepickupadmin/general/logo');
        if ($path) {
            $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'paypal/images/' . $path;
        } else {
            $frontendStore = $this->getStoreByCode('default');
            $logoConfig = Mage::getStoreConfig('design/header/logo_src', $frontendStore->getId());
            $params = array(
                '_area' => 'frontend',
                '_store' => $frontendStore
            );
            $path = Mage::getDesign()->getSkinUrl($logoConfig, $params);
        }
        return $path;
    }

    public function getStoreByCode($storeCode)
    {
        $stores = array_keys(Mage::app()->getStores());
        foreach ($stores as $id) {
            $store = Mage::app()->getStore($id);
            if ($store->getCode() == $storeCode) {
                return $store;
            }
        }
        return null; // if not found
    }

    /**
     * Retrieve all order state
     *
     * @return boolean
     */
    public function useJqueryCDN()
    {
        return Mage::getStoreConfig(self::USE_JQUERY_CDN_CONFIG_PATH);
    }
}