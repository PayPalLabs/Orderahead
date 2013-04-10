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
class Paypal_Instorepickupadmin_Block_Adminhtml_Page_Js extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Get Application Settings Json
     *
     * @return string
     */
    public function getAppSettingsJson()
    {
        $settings = array(
            'urls' => array(
                'SECTION_LOGIN' => $this->getUrl('adminhtml/instorepickupadmin_order/login'),
                'SECTION_SEARCH_ORDER' => $this->getUrl('adminhtml/instorepickupadmin_order/search'),
                'SECTION_SEARCH_ORDER_RESULT' => $this->getUrl('adminhtml/instorepickupadmin_order/searchresult'),
                'SECTION_ORDER_DETAIL' => $this->getUrl('adminhtml/instorepickupadmin_order/detail'),
                'SECTION_SAVE_ORDER' => $this->getUrl('adminhtml/instorepickupadmin_order/saveOrder'),
                'SECTION_REFUND_ORDER' => $this->getUrl('adminhtml/instorepickupadmin_creditmemo/save'),
            ),
            'formKey' => $this->getFormKey()
        );
        return $this->helper('core')->jsonEncode($settings);
    }
}
