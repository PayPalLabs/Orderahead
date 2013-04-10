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
class Paypal_Instorepickupadmin_Block_Adminhtml_Sales_Order_Search extends Paypal_Instorepickupadmin_Block_Adminhtml_Section
{
    const XML_PATH_ORDER_STATES = 'global/sales/order/states';
    public function __construct()
    {
        $this->setSectionId(Paypal_Instorepickupadmin_Helper_Data::SECTION_SEARCH_ORDER);
        $this->setSectionTitle($this->__('Search'));
    }

    /**
     * Get Form Action Url
     *
     * @return string $url
     */
    public function getFormAction(){
        $this->getFormKey();
        return $this->getUrl('adminhtml/instorepickupadmin_order/searchresult');
    }

    /**
     * Retrieve all order state
     *
     * @return array
     */
    public function getOrderStates()
    {
        $statesConfig = Mage::getConfig()->getNode(self::XML_PATH_ORDER_STATES);

        $states = array();

        foreach ($statesConfig->children() as $state => $node) {
            $states[$state] = $node->label;
        }
        return $states;
    }

    /**
     * Retrieve all order state
     *
     * @return array
     */
    public function getPlaces()
    {
        $places = false;
        if (Mage::helper('core')->isModuleEnabled('Paypal_Pickup')) {
            $places = Mage::getResourceModel('paypal_pickup/place_collection');
        }
        return $places;
    }
}
