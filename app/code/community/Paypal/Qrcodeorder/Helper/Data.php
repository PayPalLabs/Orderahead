<?php
/**
 * Qrcodeorder
 *
 * @package      :  Paypal_Qrcodeorder
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

class Paypal_Qrcodeorder_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStaticBlock(){
        $staticBlock = Mage::getModel('core/email_template_filter')->filter(
            Mage::getModel('cms/block')
                ->load(Mage::getStoreConfig('checkout/qrcode/display_text'))
                ->getContent()
        );
        if(is_null($staticBlock)){
            $staticBlock = '';
        }
        return $staticBlock;
    }
}
