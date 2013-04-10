<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Pickup
 * @version      :  0.1.2
 * @since        :  Magento 1.7
 * @author       :  Paypal  http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  18/03/2013
 *
 */

$installer = $this;

$installer->startSetup();


/**
 * Add column timezone in table 'paypal_pickup/place'
 */

$installer->run("
  ALTER TABLE {$this->getTable('paypal_pickup/place')}
  ADD `timezone` VARCHAR(255) NULL DEFAULT '';
");


$installer->endSetup();