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
CREATE TABLE " . TABLE_PRODUCT_TABS . " (
  id int(11) NOT NULL,
  define varchar(199) NOT NULL,
  sort_order int(11) NOT NULL DEFAULT '0',
  core tinyint(4) NOT NULL DEFAULT '0',
  product_type_id varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";
");

/*
 * Table structure for table `product_type_fields`
 */
$db->Execute("
CREATE TABLE " . TABLE_PRODUCT_FIELDS . " (
  id int(11) NOT NULL,
  name varchar(199) NOT NULL,
  type int(11) NOT NULL DEFAULT '0',
  description varchar(199) NOT NULL,
  default_value varchar(199) NOT NULL,
  select_value_id varchar(199) NOT NULL,
  select_value_text varchar(1999) NOT NULL,
  length text NOT NULL,
  core tinyint(4) NOT NULL DEFAULT '0',
  language_string tinyint(4) NOT NULL DEFAULT '0',
  configuration_key varchar(199) NOT NULL DEFAULT '',
  label_define varchar(199) NOT NULL DEFAULT '',
  html_editor int(11) NOT NULL DEFAULT '1',
  alert_define varchar(199) NOT NULL DEFAULT '',
  alert_condition varchar(199) NOT NULL,
  tooltip_define varchar(199) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";
");

/*
 * Table structure for table `product_type_fields_to_type`
 */
$db->Execute("
CREATE TABLE " . TABLE_PRODUCT_FIELDS_TO_TYPE . " (
  configuration_id int(11) NOT NULL,
  product_type_id int(11) NOT NULL,
  field_id int(11) NOT NULL,
  field_name varchar(199) NOT NULL,
  sort_order int(11) NOT NULL,
  tab_id int(11) NOT NULL,
  show_in_frontend tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";
");

/*
 * Table structure for table `product_type_field_types`
 */
$db->Execute("
CREATE TABLE " . TABLE_PRODUCT_FIELD_TYPES . " (
  id int(11) NOT NULL,
  text varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";
");

/*
 * Indexes for table `product_tabs`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_TABS . "
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY define (define);
");

/*
 * Indexes for table `product_type_fields`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_FIELDS . "
  ADD PRIMARY KEY (id) USING BTREE,
  ADD UNIQUE KEY unq_field_name (name) USING BTREE;
");

/*
 * Indexes for table `product_type_fields_to_type`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
  ADD PRIMARY KEY (configuration_id),
  ADD UNIQUE KEY product_type_id (product_type_id,field_id);
");

/*
 * Indexes for table `product_type_field_types`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_FIELD_TYPES . "
  ADD PRIMARY KEY (id);
");

/*
 * AUTO_INCREMENT for table `product_tabs`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_TABS . "
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;
");

/*
 * AUTO_INCREMENT for table `product_type_fields`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_FIELDS . "
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;
");

/*
 * AUTO_INCREMENT for table `product_type_fields_to_type`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
  MODIFY configuration_id int(11) NOT NULL AUTO_INCREMENT;
");

/*
 * AUTO_INCREMENT for table `product_type_field_types`
 */
$db->Execute("
ALTER TABLE " . TABLE_PRODUCT_FIELD_TYPES . "
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;
");
