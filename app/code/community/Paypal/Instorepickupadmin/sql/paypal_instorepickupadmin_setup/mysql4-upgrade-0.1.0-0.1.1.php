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
    ->addColumn($table, 'username', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 40,
    'nullable'  => true,
    'comment'   => 'Admin user name'
));
$installer->getConnection()
    ->addColumn($table, 'userrole', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 50,
    'nullable'  => true,
    'comment'   => 'Admin user role'
));
$installer->endSetup();