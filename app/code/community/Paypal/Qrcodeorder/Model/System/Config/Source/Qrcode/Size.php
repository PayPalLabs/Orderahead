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
class Paypal_Qrcodeorder_Model_System_Config_Source_Qrcode_Size
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array();
        for($i=1;$i<=10;$i++){
            $this->_options[] = array(
                'value' => $i,
                'label' => $i
            );
        }
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}
