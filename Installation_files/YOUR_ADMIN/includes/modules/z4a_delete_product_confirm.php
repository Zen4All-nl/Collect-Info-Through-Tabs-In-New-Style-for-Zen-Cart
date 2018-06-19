<?php

/**
 * @package admin
 * @copyright (c) 2008-2017, zen4All
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: z4a_delete_product_confirm.php Zen4All
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
// NOTE: Debug code left in to help with creating additional product type delete-scripts

$do_delete_flag = false;
//echo 'products_id=' . $_POST['products_id'] . '<br />';
if (isset($_POST['products_id']) && isset($_POST['product_categories']) && is_array($_POST['product_categories'])) {
  $product_id = zen_db_prepare_input($_POST['products_id']);
  $product_categories = $_POST['product_categories'];
  $do_delete_flag = true;
  if (!isset($delete_linked)) {
    $delete_linked = 'true';
  }
}

if (zen_not_null($cascaded_prod_id_for_delete) && zen_not_null($cascaded_prod_cat_for_delete)) {
  $product_id = $cascaded_prod_id_for_delete;
  $product_categories = $cascaded_prod_cat_for_delete;
  $do_delete_flag = true;
  // no check for $delete_linked here, because it should already be passed from categories.php
}

if ($do_delete_flag) {
  //--------------PRODUCT_TYPE_SPECIFIC_INSTRUCTIONS_GO__BELOW_HERE--------------------------------------------------------
  if (file_exists(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_delete_product_confirm.php')) {
    require(DIR_WS_MODULES . $zc_products->get_handler($product_type) . '/z4a_delete_product_confirm.php');
  }
  //--------------PRODUCT_TYPE_SPECIFIC_INSTRUCTIONS_GO__ABOVE__HERE--------------------------------------------------------
  // now do regular non-type-specific delete:
  // remove product from all its categories:
  for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
    $db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                  WHERE products_id = " . (int)$product_id . "
                  AND categories_id = " . (int)$product_categories[$i]);
  }
  // confirm that product is no longer linked to any categories
  $count_categories = $db->Execute("SELECT COUNT(categories_id) AS total
                                    FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                    WHERE products_id = " . (int)$product_id);
  // echo 'count of category links for this product=' . $count_categories->fields['total'] . '<br />';
  // if not linked to any categories, do delete:
  if ($count_categories->fields['total'] == '0') {
    zen_remove_product($product_id, $delete_linked);
  }
} // endif $do_delete_flag
// if this is a single-product delete, redirect to categories page
// if not, then this file was called by the cascading delete initiated by the category-delete process
if ($action == 'delete_product_confirm')
  zen_redirect(zen_href_link(FILENAME_Z4A_CATEGORIES_PRODUCT_LISTING, 'cPath=' . $cPath));
