<?php

/*
 *
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version Author: bislewl  2/19/2018 12:18 PM Modified in zencart_additional_images_uploader
 *
 */

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0
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

  $admin_page = 'catalogZen4AllCategories';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_CATALOG_ZEN4ALL_CATEGORIES', 'FILENAME_ZEN4ALL_CATEGORIES', '', 'catalog', 'N', $configuration_group_id);

      $messageStack->add('Enabled Additional Images Uploader Tools Menu Item', 'success');
    }
  }

  $admin_page = 'catalogZen4llACategoriesProductListing';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_CATALOG_ZEN4ALL_CATEGORIES_PRODUCT_LISTING', 'FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING', '', 'catalog', 'Y', $configuration_group_id);

      $messageStack->add('Enabled Zen4All Cittins Categories / Products Listing', 'success');
    }
  }
  $admin_page = 'catalogZen4AllProducts';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_CATALOG_ZEN4ALL_PRODUCTS', 'FILENAME_ZEN4ALL_PRODUCTS', '', 'catalog', 'N', $configuration_group_id);

      $messageStack->add('Enabled Zen4All Cittins Products Page', 'success');
    }
  }
  $admin_page = 'catalogZen4AllProductLayoutEditor';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_ZEN4ALL_CATALOG_PRODUCT_LAYOUT', 'FILENAME_ZEN4ALL_PRODUCT_LAYOUT_EDITOR', '', 'catalog', 'Y', $configuration_group_id);

      $messageStack->add('Enabled Additional Images Uploader Tools Menu Item', 'success');
    }
  }
}
