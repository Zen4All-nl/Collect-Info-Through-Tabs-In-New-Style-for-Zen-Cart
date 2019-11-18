<?php

/**
 * 2_0_0.php
 *
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version Author: Zen4All
 */

/*
 * Table structure for table `product_tabs`
 */
$db->Execute("
CREATE TABLE IF NOT EXISTS " . TABLE_PRODUCT_TABS . " (
  id int(11) NOT NULL AUTO_INCREMENT,
  sort_order int(11) NOT NULL DEFAULT '0',
  core tinyint(4) NOT NULL DEFAULT '0',
  product_type_id varchar(64) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";
");

/*
 * Table structure for table `product_type_fields_to_type`
 */
$db->Execute("
CREATE TABLE IF NOT EXISTS " . TABLE_PRODUCT_FIELDS_TO_TYPE . " (
  configuration_id int(11) NOT NULL AUTO_INCREMENT,
  product_type int(11) NOT NULL DEFAULT '0',
  field_name varchar(199) DEFAULT NULL,
  sort_order int(11) NOT NULL DEFAULT '0',
  tab_id int(11) NOT NULL DEFAULT '0',
  show_in_frontend tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`configuration_id`),
  UNIQUE KEY `product_type_id` (`product_type`,`field_name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";
");

