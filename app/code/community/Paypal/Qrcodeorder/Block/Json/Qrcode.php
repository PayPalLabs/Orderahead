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

class Paypal_Qrcodeorder_Block_Json_Qrcode extends Paypal_Qrcodeorder_Block_Abstract
{
    public function _toHtml() {
        $url = $this->generateQrcode();
        $array = array(
            'url' => $url,
            'text' => Mage::helper("paypal_qrcodeorder")->getStaticBlock(),
            'enabled' => Mage::getStoreConfig(self::QRCODE_ENABLE_PATH),
        );
        return Mage::helper('core')->jsonEncode($array);
    }
}
