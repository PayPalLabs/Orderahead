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
class Paypal_Instorepickupadmin_Block_QrcodeOrderReceiptEmail extends Paypal_Instorepickupadmin_Block_Qrcode
{
    const QRCODE_DISPLAY_ORDER_RECEIPT_EMAIL = 'paypal_instorepickupadmin/qrcode/display_order_receipt_email';

    public function _toHtml()
    {
        if (Mage::getStoreConfig(self::QRCODE_DISPLAY_ORDER_RECEIPT_EMAIL)) {
            return '<img src="' . $this->generateQRcode() . '"/>';
        }

        return '';
    }
}
