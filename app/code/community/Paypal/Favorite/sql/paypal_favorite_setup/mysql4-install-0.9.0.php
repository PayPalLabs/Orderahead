<?php
/**
 * PayPal Favorite
 *
 * @package      :  PayPal_Favorite
 * @version      :  0.9.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  19/03/2013
 * 
 */

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `paypal_customer_id_type`;
DROP TABLE IF EXISTS `paypal_customer_favorites`;

CREATE TABLE IF NOT EXISTS `paypal_customer_id_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `paypal_customer_id_type` (`id`, `name`) VALUES
(1, 'Magento_customer_id'),
(2, 'Paypal_customer_id'),
(3, 'Session_id');

CREATE TABLE IF NOT EXISTS `paypal_customer_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` VARCHAR(40) NOT NULL,
  `customer_id_type` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_favorite_unique` (`customer_id`,`customer_id_type`,`product_id`),
  KEY `fk_customer_favorite_id_type` (`customer_id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
