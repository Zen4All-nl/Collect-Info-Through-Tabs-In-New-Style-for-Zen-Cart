<?php

/**
 * @package admin
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: ajeh  Wed Jul 9 21:58:03 2014 -0400 Modified in v1.5.5 $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$languages = zen_get_languages();

if (isset($_GET['pID']))
  $products_id = zen_db_prepare_input($_GET['pID']);
if (isset($_POST['edit_x']) || isset($_POST['edit_y'])) {
  $action = 'new_product';
} elseif ($_POST['products_model'] . $_POST['products_url'] . $_POST['products_name'] . $_POST['products_description'] != '') {
  $products_date_available = zen_db_prepare_input($_POST['products_date_available']);
  $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';
  // Data-cleaning to prevent MySQL5 data-type mismatch errors:
  $tmp_value = zen_db_prepare_input($_POST['products_quantity']);
  $products_quantity = (!zen_not_null($tmp_value) || $tmp_value == '' || $tmp_value == 0) ? 0 : $tmp_value;
  $tmp_value = zen_db_prepare_input($_POST['products_price']);
  $products_price = (!zen_not_null($tmp_value) || $tmp_value == '' || $tmp_value == 0) ? 0 : $tmp_value;
  $tmp_value = zen_db_prepare_input($_POST['products_weight']);
  $products_weight = (!zen_not_null($tmp_value) || $tmp_value == '' || $tmp_value == 0) ? 0 : $tmp_value;
  $tmp_value = zen_db_prepare_input($_POST['manufacturers_id']);
  $manufacturers_id = (!zen_not_null($tmp_value) || $tmp_value == '' || $tmp_value == 0) ? 0 : $tmp_value;

  $sql_data_array = array('products_quantity' => $products_quantity,
    'products_type' => zen_db_prepare_input($_GET['product_type']),
    'products_model' => zen_db_prepare_input($_POST['products_model']),
    'products_price' => $products_price,
    'products_date_available' => $products_date_available,
    'products_weight' => $products_weight,
    'products_status' => zen_db_prepare_input((int)$_POST['products_status']),
    'products_virtual' => zen_db_prepare_input((int)$_POST['products_virtual']),
    'products_tax_class_id' => zen_db_prepare_input((int)$_POST['products_tax_class_id']),
    'manufacturers_id' => $manufacturers_id,
    'products_quantity_order_min' => zen_db_prepare_input(($_POST['products_quantity_order_min'] == 0 ? 1 : $_POST['products_quantity_order_min'])),
    'products_quantity_order_units' => zen_db_prepare_input(($_POST['products_quantity_order_units'] == 0 ? 1 : $_POST['products_quantity_order_units'])),
    'products_priced_by_attribute' => zen_db_prepare_input((int)$_POST['products_priced_by_attribute']),
    'product_is_free' => zen_db_prepare_input((int)$_POST['product_is_free']),
    'product_is_call' => zen_db_prepare_input((int)$_POST['product_is_call']),
    'products_quantity_mixed' => zen_db_prepare_input($_POST['products_quantity_mixed']),
    'product_is_always_free_shipping' => zen_db_prepare_input((int)$_POST['product_is_always_free_shipping']),
    'products_qty_box_status' => zen_db_prepare_input($_POST['products_qty_box_status']),
    'products_quantity_order_max' => zen_db_prepare_input($_POST['products_quantity_order_max']),
    'products_sort_order' => (int)zen_db_prepare_input($_POST['products_sort_order']),
    'products_discount_type' => zen_db_prepare_input($_POST['products_discount_type']),
    'products_discount_type_from' => zen_db_prepare_input($_POST['products_discount_type_from']),
    'products_price_sorter' => zen_db_prepare_input($_POST['products_price_sorter'])
  );

  // when set to none remove from database
  // is out dated for browsers use radio only
  $sql_data_array['products_image'] = zen_db_prepare_input($_POST['products_image']);
  $new_image = 'true';

  if ($_POST['image_delete'] == 1) {
    $sql_data_array['products_image'] = '';
    $new_image = 'false';
  }

  if ($action == 'insert_product') {
    $insert_sql_data = array(
      'products_date_added' => 'now()',
      'master_categories_id' => (int)$current_category_id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

    zen_db_perform(TABLE_PRODUCTS, $sql_data_array);
    $products_id = zen_db_insert_id();

    // reset products_price_sorter for searches etc.
    zen_update_products_price_sorter($products_id);

    $db->Execute("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                  values ('" . (int)$products_id . "', '" . (int)$current_category_id . "')");

    zen_record_admin_activity('New product ' . (int)$products_id . ' added via admin console.', 'info');

    ///////////////////////////////////////////////////////
    //// INSERT PRODUCT-TYPE-SPECIFIC *INSERTS* HERE //////
    ////    *END OF PRODUCT-TYPE-SPECIFIC INSERTS* ////////
    ///////////////////////////////////////////////////////
  } elseif ($action == 'update_product') {
    $update_sql_data = array(
      'products_last_modified' => 'now()',
      'master_categories_id' => ($_POST['master_category'] > 0 ? zen_db_prepare_input($_POST['master_category']) : zen_db_prepare_input($_POST['master_categories_id'])));

    $sql_data_array = array_merge($sql_data_array, $update_sql_data);

    zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");

    zen_record_admin_activity('Updated product ' . (int)$products_id . ' via admin console.', 'info');

    // reset products_price_sorter for searches etc.
    zen_update_products_price_sorter((int)$products_id);

    ///////////////////////////////////////////////////////
    //// INSERT PRODUCT-TYPE-SPECIFIC *UPDATES* HERE //////
    ////    *END OF PRODUCT-TYPE-SPECIFIC UPDATES* ////////
    ///////////////////////////////////////////////////////
  }

  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $language_id = $languages[$i]['id'];

    $sql_data_array = array(
      'products_name' => zen_db_prepare_input($_POST['products_name'][$language_id]),
      'products_description' => zen_db_prepare_input($_POST['products_description'][$language_id]),
      'products_url' => zen_db_prepare_input($_POST['products_url'][$language_id]));

    if ($action == 'insert_product') {
      $insert_sql_data = array(
        'products_id' => (int)$products_id,
        'language_id' => (int)$language_id);

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
    } elseif ($action == 'update_product') {
      zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and language_id = '" . (int)$language_id . "'");
    }
  }

  // add meta tags

  $sql_data_array = array(
    'metatags_title_status' => zen_db_prepare_input($_POST['metatags_title_status']),
    'metatags_products_name_status' => zen_db_prepare_input($_POST['metatags_products_name_status']),
    'metatags_model_status' => zen_db_prepare_input($_POST['metatags_model_status']),
    'metatags_price_status' => zen_db_prepare_input($_POST['metatags_price_status']),
    'metatags_title_tagline_status' => zen_db_prepare_input($_POST['metatags_title_tagline_status'])
  );

  if ($action == 'insert_product') {
    zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
  } elseif ($action == 'update_product') {
    $update_sql_data = array('products_last_modified' => 'now()');

    $sql_data_array = array_merge($sql_data_array, $update_sql_data);
//die('UPDATE PRODUCTS ID:' . (int)$products_id . ' - ' . sizeof($sql_data_array));
    zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
  }

// check if new meta tags or existing
  $check_meta_tags_description = $db->Execute("SELECT products_id FROM " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " WHERE products_id='" . (int)$products_id . "'");
  if ($check_meta_tags_description->RecordCount() <= 0) {
    $action = 'insert_product';
  }

  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $language_id = $languages[$i]['id'];

    $sql_data_array = array(
      'metatags_title' => zen_db_prepare_input($_POST['metatags_title'][$language_id]),
      'metatags_keywords' => zen_db_prepare_input($_POST['metatags_keywords'][$language_id]),
      'metatags_description' => zen_db_prepare_input($_POST['metatags_description'][$language_id]));

    if ($action == 'insert_product') {
      $insert_sql_data = array(
        'products_id' => (int)$products_id,
        'language_id' => (int)$language_id);

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      zen_db_perform(TABLE_META_TAGS_PRODUCTS_DESCRIPTION, $sql_data_array);
    } elseif ($action == 'update_product') {
      if ($n == 1 && empty($_POST['metatags_title'][$language_id]) && empty($_POST['metatags_keywords'][$language_id]) && empty($_POST['metatags_description'][$language_id])) {
        $remove_products_metatag = "DELETE FROM " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " WHERE products_id = '" . (int)$products_id . "' AND language_id = '" . (int)$language_id . "'";
        $db->Execute($remove_products_metatag);
      } else {

        zen_db_perform(TABLE_META_TAGS_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and language_id = '" . (int)$language_id . "'");
      }
    }
  }

  $extraTabsupate = dirList(DIR_WS_MODULES . 'extra_tabs/', 'tab_update_product.php');
  if (isset($extraTabsupate) && $extraTabsupate != '') {
    foreach ($extraTabsupate as $tabUpdate) {
      include($tabUpdate);
    }
  }

  zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . (isset($_POST['search']) ? '&search=' . $_POST['search'] : '')));
} else {
  $messageStack->add_session(ERROR_NO_DATA_TO_SAVE, 'error');
  zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . (isset($_POST['search']) ? '&search=' . $_POST['search'] : '')));
}