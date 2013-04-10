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
class Paypal_Instorepickupadmin_Block_Adminhtml_Sales_Order_Detail_Items extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getItemRenderer($type)
    {
        $defaultType = 'default_mobile';
        $typeMapping = array(
                        'bundle' => 'bundle_mobile'
                    );
        if(!empty($typeMapping[$type])){
            $type = $typeMapping[$type];
        }
        else{
            $type = $defaultType;
        }
        return parent::getItemRenderer($type);
    }
}
