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
              VALUES ('Show Name column', 'ZEN4ALL_CITTINS_COLUMN_NAME', 'true', 'Show the Name column\r\ndefault: true', " . $configuration_group_id . ", 1, now(), 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                     ('Show Model column', 'ZEN4ALL_CITTINS_COLUMN_MODEL', 'true', 'Show the Model column\r\ndefault: true', " . $configuration_group_id . ", 2, now(), 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                     ('Show Price column', 'ZEN4ALL_CITTINS_COLUMN_PRICE', 'true', 'Show the Price column\r\ndefault: true', " . $configuration_group_id . ", 3, now(), 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                     ('Show Quantity column', 'ZEN4ALL_CITTINS_COLUMN_QUANTITY', 'true', 'Show the Quantity column\r\ndefault: true', " . $configuration_group_id . ", 4, now(), 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                     ('Show Status column', 'ZEN4ALL_CITTINS_COLUMN_STATUS', 'true', 'Show the Status column\r\ndefault: true', " . $configuration_group_id . ", 5, now(), 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                     ( 'Show Sort Order column', 'ZEN4ALL_CITTINS_COLUMN_SORT', 'true', 'Show the Sort Order column\r\ndefault: true', " . $configuration_group_id . ", 6, now(), 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                     ('Show Image column', 'ZEN4ALL_CITTINS_COLUMN_IMAGE', 'false', 'Show the Image column\r\ndefault: false', " . $configuration_group_id . ", 7, now(), 'zen_cfg_select_option(array(\'true\', \'false\'),');");
