<?php
/**
 * Qrcode
 *
 * @package      :  Paypal_Qrcode
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

set_include_path(get_include_path().PS.Mage::getBaseDir('lib').DS.'phpqrcode');
require_once('qrlib.php');

class Paypal_Qrcode_Model_Qrcode extends Mage_Core_Model_Abstract
{
    public function generateQrcode($text, $filename, $qrcode_folder, $errorCorrectionLevel, $matrixPointSize) {
        if(!file_exists($qrcode_folder)){
            mkdir($qrcode_folder, 0777, true);
        }

        $filename = md5($filename.'|'.$errorCorrectionLevel.'|'.$matrixPointSize);

        if (!file_exists($filename)) {
            QRcode::png($text, $qrcode_folder . $filename . '.png', $errorCorrectionLevel, $matrixPointSize);
        }

        $root_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        return $root_url.'/media/ordercodes/qrcode/'.$filename.'.png';
    }
}
