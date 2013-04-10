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
class Paypal_Pickup_Block_Adminhtml_Place extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_place';
        $this->_blockGroup = 'paypal_pickup';
        $this->_headerText = Mage::helper('paypal_pickup')->__('Manage Places');
        $this->_addButtonLabel = Mage::helper('paypal_pickup')->__('Add Place');
        parent::__construct();
    }
}