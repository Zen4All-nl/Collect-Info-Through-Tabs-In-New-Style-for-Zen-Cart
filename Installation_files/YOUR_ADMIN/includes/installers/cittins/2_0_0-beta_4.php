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
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function)
              VALUES ('Default Sort Order', 'ZEN4ALL_CITTINS_DEFAULT_LISTING_SORTORDER', '3', 'Set the default sort order for the category product listing<br>\r\n<br>\r\nCategories/Products Display Sort Order<br>\r\n1 = Categories/Products Id Ascending<br>\r\n2 = Categories/Products Id Decending<br>\r\n3 = Categories/Products Name Ascending<br>\r\n4 = Categories/Products Name Decending<br>\r\n5 = Products Model Ascending<br>\r\n6 = Products Model Decending<br>\r\n7 = Products Price Ascending<br>\r\n8 = Products Price Decending<br>\r\n9 = Categories/Products Quantity Ascending<br>\r\n10 = Categories/Products Quantity Decending<br>\r\n11 = Categories/Products Sort Order Ascending<br>\r\n12 = Categories/Products Sort Order Decending<br>', " . $configuration_group_id . ", 8, now(), 'zen_cfg_select_drop_down(array(\r\narray(\'id\'=>\'1\', \'text\'=>\'Categories/Products Id Ascending\'),\r\narray(\'id\'=>\'2\', \'text\'=>\'Categories/Products Id Decending\'),\r\narray(\'id\'=>\'3\', \'text\'=>\'Categories/Products Name Ascending\'),\r\narray(\'id\'=>\'4\', \'text\'=>\'Categories/Products Name Decending\'),\r\narray(\'id\'=>\'5\', \'text\'=>\'Products Model Ascending\'),\r\narray(\'id\'=>\'6\', \'text\'=>\'Products Model Decending\'),\r\narray(\'id\'=>\'7\', \'text\'=>\'Products Price Ascending\'),\r\narray(\'id\'=>\'8\', \'text\'=>\'Products Price Decending\'),\r\narray(\'id\'=>\'9\', \'text\'=>\'Categories/Products Quantity Ascending\'),\r\narray(\'id\'=>\'10\', \'text\'=>\'Categories/Products Quantity Decending\'),\r\narray(\'id\'=>\'11\', \'text\'=>\'Categories/Products Sort Order Ascending\'),\r\narray(\'id\'=>\'12\', \'text\'=>\'Categories/Products Sort Order Decending\')\r\n),');");