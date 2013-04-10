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
class Paypal_Instorepickupadmin_Block_Adminhtml_Sales_Order_Detail_Items_Renderer_Bundle extends Mage_Bundle_Block_Adminhtml_Sales_Order_Items_Renderer
{
    public function isChangeDeliverQty(){
        $result = false;
        if($this->getOrder()->canShip()){
            $result = true;
        }
        return $result;
    }

    /**
     * Getting all available childs for Invoice, Shipmen or Creditmemo item
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getChilds($item)
    {
        $_itemsArray = array();

        $_items = $item->getOrder()->getAllItems();

        if ($_items) {
            foreach ($_items as $_item) {
                if ($parentItem = $_item->getParentItem()) {
                    $_itemsArray[$parentItem->getId()][$_item->getId()] = $_item;
                } else {
                    //$_itemsArray[$_item->getId()][$_item->getId()] = $_item;
                }
            }
        }

        if (isset($_itemsArray[$item->getId()])) {
            return $_itemsArray[$item->getId()];
        } else {
            return null;
        }
    }
}
