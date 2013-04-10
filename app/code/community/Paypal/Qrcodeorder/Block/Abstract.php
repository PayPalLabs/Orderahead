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

class Paypal_Qrcodeorder_Block_Abstract extends Mage_Core_Block_Template
{
    const QRCODE_ENABLE_PATH = 'checkout/qrcode/display_success_page';
    const QRCODE_ERROR_CORRECTION_LEVEL_CONFIG_PATH = 'checkout/qrcode/ecc';
    const QRCODE_MATRIX_POINT_SIZE_CONFIG_PATH = 'checkout/qrcode/size';
    const QRCODE_DISPLAY_TEXT_PATH = 'checkout/qrcode/display_text';

    public function generateQrcode() {
        if (Mage::getStoreConfig(self::QRCODE_ENABLE_PATH)) {
            if (Mage::registry('current_order')) {
                $order_id = Mage::registry('current_order')->getId();
            } elseif ($orderId = Mage::getSingleton('checkout/session')->getLastOrderId()) {
                $order_id = Mage::getModel('sales/order')->load($orderId)->getId();
            }

            if (!$order_id) {
                return '';
            }

            $filename = $order_id;
            $qrcode_folder = Mage::getBaseDir('base') . '/media/ordercodes/qrcode/';
            $errorCorrectionLevel = Mage::getStoreConfig(self::QRCODE_ERROR_CORRECTION_LEVEL_CONFIG_PATH);
            $matrixPointSize = Mage::getStoreConfig(self::QRCODE_MATRIX_POINT_SIZE_CONFIG_PATH);
            $data = $this->getUrl(
                'adminhtml/instorepickupadmin_order/detail',
                array(
                    'id'=>$order_id,
                    'hash'=>$this->helper('paypal_instorepickupadmin')->getOrderDetailHash($order_id)));

            $url = Mage::getModel('paypal_qrcode/qrcode')->generateQrcode(
                $data,
                $filename,
                $qrcode_folder,
                $errorCorrectionLevel,
                $matrixPointSize,
                2);
        }
        return $url;
    }
}
