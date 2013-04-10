<?php
/**
 * Pickup
 *
 * @package      :  Paypal_Locationhere
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
$installer = $this;
$installer->startSetup();

/**
 * Create table 'paypal_pickup/place'
 */
$installer->run("

    CREATE TABLE IF NOT EXISTS {$this->getTable('paypal_pickup/place')} (
        `place_id` int(11) unsigned NOT NULL auto_increment,
        `place_code` varchar(255) NOT NULL default '',
        `place_name` varchar(255) NOT NULL default '',
        `status` smallint(6) NOT NULL default '0',
        `description` text NOT NULL default '',
        `open_datetime` text NOT NULL default '',
        `address` varchar(255) NOT NULL default '',
        `city` varchar(255) NOT NULL default '',
        `region` varchar(255) NOT NULL default '',
        `region_id` int NULL,
        `postcode` varchar(255) NOT NULL default '',
        `country` varchar(255) NOT NULL default '',
        `longitude` varchar(255) NOT NULL default '',
        `latitude` varchar(255) NOT NULL default '',
        `phone` varchar(255) NOT NULL default '',
        `email` varchar(255) NOT NULL default '',
        `allow_pickup_datetime` smallint(6) NOT NULL default '0',
        `store_hour_exceptions` text NOT NULL default '',
        `type` smallint(6) NOT NULL default '0',
        `durations_options` varchar(255) NOT NULL default '',
        `datetime_min_days` int NULL,
        `datetime_max_days` int NULL,
        `datetime_enable_time_selector` smallint(6) NOT NULL default '0',
        `datetime_minutes_displayed` int NULL,
        PRIMARY KEY (`place_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('paypal_locationhere')};
    CREATE TABLE {$this->getTable('paypal_locationhere')} (
        `assignment_id` int(11) unsigned NOT NULL auto_increment,
        `place_id` int(11) unsigned NOT NULL,  
        `paypal_here_location_id` varchar(255) NOT NULL,                
        PRIMARY KEY (`assignment_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->run("
  ALTER TABLE {$this->getTable('paypal_pickup/place')}
  ADD `paypal_here_api_enabled` smallint(6) NOT NULL default '0';
");
$installer->run("
  ALTER TABLE {$this->getTable('paypal_pickup/place')}
  ADD `last_availability` smallint(6) NOT NULL default '0';
");
$installer->run("
  ALTER TABLE {$this->getTable('paypal_pickup/place')}
  ADD `paypal_here_store_view` int(11) unsigned NOT NULL default '1';
");
$installer->endSetup();
