<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Pickup
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal  http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */

$installer = $this;

$installer->startSetup();


/**
 * Add column pickup_time in table 'sales_flat_order' and 'sales_flat_quote'
 */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$pickupTime = array(
        'type'              => 'text',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'pickup_time',
        'input'             => '',
        'class'             => '',
        'source'            => '',
        'global'            => 1,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'visible_in_advanced_search' => false,
    'unique'            => false
);

//$setup->addAttribute('order', "pickup_time", $pickupTime);
$setup->getConnection()->addColumn($installer->getTable('sales/order'), 'pickup_time', 'TEXT DEFAULT NULL');
//$setup->addAttribute('quote', "pickup_time", $pickupTime);
$setup->getConnection()->addColumn($installer->getTable('sales/quote'), 'pickup_time', 'TEXT DEFAULT NULL');


$installer->endSetup();