<?php
/**
 * Pickup
 *
 * @package      :  Paypal_LocationHere
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
?>
<?php
class Paypal_Locationhere_Model_Mysql4_Locationhere extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct(){
        $this->_init('paypal_locationhere/locationhere', 'assignment_id');
    }
}