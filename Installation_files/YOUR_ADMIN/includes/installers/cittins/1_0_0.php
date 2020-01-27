<?php

/*
 *
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version Author: bislewl  2/19/2018 12:18 PM Modified in zencart_additional_images_uploader
 *
 */

  $admin_page = 'configZen4AllCittins';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_CONFIGURATION_ZEN4ALL_CITTINS', 'FILENAME_CONFIGURATION', 'gID=' . $configuration_group_id, 'configuration', 'Y', $configuration_group_id);

      $messageStack->add('Enabled Zen4All Configuration Menu Item', 'success');
    }
  }

  $admin_page = 'catalogCittinsCategories';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_CATALOG_CITTINS_CATEGORIES', 'FILENAME_CITTINS_CATEGORIES', '', 'catalog', 'N', $configuration_group_id);

      $messageStack->add('Enabled Zen4All Cittins Categories', 'success');
    }
  }

  $admin_page = 'catalogZen4llACategoriesProductListing';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_CATALOG_CITTINS_CATEGORIES_PRODUCT_LISTING', 'FILENAME_CITTINS_CATEGORIES_PRODUCT_LISTING', '', 'catalog', 'Y', $configuration_group_id);

      $messageStack->add('Enabled Zen4All Cittins Categories / Products Listing', 'success');
    }
  }
  $admin_page = 'catalogCittinsProducts';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_CATALOG_CITTINS_PRODUCTS', 'FILENAME_ZEN4ALL_PRODUCTS', '', 'catalog', 'N', $configuration_group_id);

      $messageStack->add('Enabled Zen4All Cittins Products Page', 'success');
    }
  }
  $admin_page = 'catalogCittinsProductLayoutEditor';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_ZEN4ALL_CATALOG_PRODUCT_LAYOUT', 'FILENAME_CITTINS_PRODUCT_LAYOUT_EDITOR', '', 'catalog', 'Y', $configuration_group_id);

      $messageStack->add('Enabled Cittins Product Layout Editor Menu Item', 'success');
    }
  }
