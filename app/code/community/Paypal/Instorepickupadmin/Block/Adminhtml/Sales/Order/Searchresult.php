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
class Paypal_Instorepickupadmin_Block_Adminhtml_Sales_Order_Searchresult extends Paypal_Instorepickupadmin_Block_Adminhtml_Section
{

    public function __construct()
    {
        $this->setSectionId(Paypal_Instorepickupadmin_Helper_Data::SECTION_SEARCH_ORDER_RESULT);
        $this->setSectionTitle($this->__('Search Result'));
    }

    /**
     * Get Order Collection
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getCollection()
    {
        $collection = false;
        if(Mage::registry('order_collection')){
            $collection = Mage::registry('order_collection');
        }
        return $collection;
    }

    public function getOrderDetailUrl($orderId){
        return $this->getUrl('adminhtml/instorepickupadmin_order/detail',array('id'=>$orderId));
    }

    protected function _beforeToHtml(){

        $toolbarBlock = $this->getLayout()->getBlock('toolbar');
        if($toolbarBlock){
            $title = $this->getCollection()->getSize() . ' '. $this->__('orders found');
            $toolbarBlock->setTitle($title);
        }
        return parent::_beforeToHtml();
    }
}
