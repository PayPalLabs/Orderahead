<?php

//Added for "paypal_customer_id" eav
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order', 'paypal_customer_id', array(
		'input'         => 'text',
		'type'          => 'varchar',
		'label'         => 'Paypal Customer ID',
		'global'            => 1,
        'visible'           => 1,
        'required'          => 0,
        'user_defined'      => 1,
        'searchable'        => 0,
        'filterable'        => 0,
        'comparable'        => 0,
        'visible_on_front'  => 1,
        'visible_in_advanced_search' => 0,
        'unique'            => 0,
        'is_configurable'   => 0,
        'position'          => 1
));

/** Start : Adding paypal checkin order statuses **/
$processingStatus = Mage::getModel('sales/order_status');

$processingStatus->setStatus(Paypal_Checkin_Helper_Data::ORDER_STATUS_CODE_PAID)->setLabel(Paypal_Checkin_Helper_Data::ORDER_STATUS_LABEL_PAID)
		->assignState(Mage_Sales_Model_Order::STATE_PROCESSING) 
		->save();

$deliveredStatus = Mage::getModel('sales/order_status');

$deliveredStatus->setStatus(Paypal_Checkin_Helper_Data::ORDER_STATUS_CODE_DELIVERED)->setLabel(Paypal_Checkin_Helper_Data::ORDER_STATUS_LABEL_DELIVERED)
		->assignState(Mage_Sales_Model_Order::STATE_COMPLETE) 
		->save();

$rejectedStatus = Mage::getModel('sales/order_status');

$rejectedStatus->setStatus(Paypal_Checkin_Helper_Data::ORDER_STATUS_CODE_REJECTED)->setLabel(Paypal_Checkin_Helper_Data::ORDER_STATUS_LABEL_REJECTED)
				->assignState(Mage_Sales_Model_Order::STATE_CANCELED)
				->save();

$partiallyDelivered = Mage::getModel('sales/order_status');

$partiallyDelivered->setStatus(Paypal_Checkin_Helper_Data::ORDER_STATUS_CODE_PARTIALLY_DELIVERED)->setLabel(Paypal_Checkin_Helper_Data::ORDER_STATUS_LABEL_PARTIALLY_DELIVERED)
				->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
				->save();
/** End : Adding paypal checkin order statuses **/

$installer->endSetup();