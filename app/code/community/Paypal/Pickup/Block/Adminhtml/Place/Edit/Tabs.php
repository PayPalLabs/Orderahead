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
class Paypal_Pickup_Block_Adminhtml_Place_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('place_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('paypal_pickup')->__('Place'));
    }

    protected function _beforeToHtml() {
        $this->addTab('general_section', array(
            'label' => Mage::helper('paypal_pickup')->__('Place Information'),
            'title' => Mage::helper('paypal_pickup')->__('Place Information'),
            'content' => $this->getLayout()->createBlock('paypal_pickup/adminhtml_place_edit_tabs_form')->toHtml(),
        ));
        
        $this->addTab('product_section', array(
            'label' => Mage::helper('paypal_pickup')->__('Order Fulfillment Time Configuration'),
            'title' => Mage::helper('paypal_pickup')->__('Order Fulfillment Time Configuration'),
            'content' => $this->getLayout()->createBlock('paypal_pickup/adminhtml_place_edit_tabs_ordertime')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}