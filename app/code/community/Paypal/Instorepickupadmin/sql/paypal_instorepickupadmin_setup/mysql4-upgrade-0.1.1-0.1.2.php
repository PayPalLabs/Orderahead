<?php
/**
 * Place block
 *
 * @author Hieptq
 * @category   Paypal
 * @package    Paypal_Instorepickupadmin
 * @copyright  Copyright (c) 2013 SmartOSC (http://www.smartosc.com)
 *
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('sales/order_status_history');
$installer->getConnection()
    ->addColumn($table, 'is_security', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'length'    => 50,
    'nullable'  => true,
    'comment'   => 'Check security code'
));
$installer->endSetup();