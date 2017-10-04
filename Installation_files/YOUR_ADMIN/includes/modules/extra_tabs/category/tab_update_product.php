<?php

// update product to categories

switch ($action) {
  case 'update_product':
    $zv_check_master_categories_id = 'true';
    $new_categories_sort_array[] = $_POST['current_master_categories_id'];
    $current_master_categories_id = $_POST['current_master_categories_id'];

    // set the linked products master_categories_id product(s)
    for ($i = 0, $n = sizeof($_POST['categories_add']); $i < $n; $i++) {
      // is current master_categories_id in the list?
      if ($zv_check_master_categories_id == 'true' and $_POST['categories_add'][$i] == $current_master_categories_id->fields['master_categories_id']) {
        $zv_check_master_categories_id = 'true';
        // array is set above to master category
      } else {
        $new_categories_sort_array[] = (int)$_POST['categories_add'][$i];
      }
    }

    // remove existing products_to_categories for current product
    $db->Execute("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                  WHERE products_id='" . (int)$products_id . "'");

    $reset_master_categories_id = '';
    $old_master_categories_id = $current_master_categories_id;
    // add products to categories in order of master_categories_id first then others
    $verify_current_category_id = false;
    for ($i = 0, $n = sizeof($new_categories_sort_array); $i < $n; $i++) {
      // is current master_categories_id in the list?
      if ($new_categories_sort_array[$i] <= 0) {
        die('I WOULD NOT ADD ' . $new_categories_sort_array[$i] . '<br>');
      } else {
        if ($current_category_id == $new_categories_sort_array[$i]) {
          $verify_current_category_id = true;
        }
        $db->Execute("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                      VALUES (" . (int)$products_id . ", " . (int)$new_categories_sort_array[$i] . ")");
        if ($reset_master_categories_id == '') {
          $reset_master_categories_id = $new_categories_sort_array[$i];
        }
        if ($old_master_categories_id == $new_categories_sort_array[$i]) {
          $reset_master_categories_id = $new_categories_sort_array[$i];
        }
      }
    }

    // reset master_categories_id in products table
    if ($zv_check_master_categories_id == 'true') {
      // make sure master_categories_id is set to current master_categories_id
      $db->Execute("UPDATE " . TABLE_PRODUCTS . "
                    SET master_categories_id='" . (int)$current_master_categories_id . "'
                    WHERE products_id='" . (int)$products_id . "'");
    } else {
      // reset master_categories_id to current_category_id because it was unselected
      $db->Execute("UPDATE " . TABLE_PRODUCTS . "
                    SET master_categories_id='" . (int)$reset_master_categories_id . "'
                    WHERE products_id='" . (int)$products_id . "'");
    }

    // recalculate price based on new master_categories_id
    zen_update_products_price_sorter((int)$products_id);

    if ($zv_check_master_categories_id == 'true') {
      $messageStack->add_session(SUCCESS_MASTER_CATEGORIES_ID, 'success');
    } else {
      $messageStack->add_session(WARNING_MASTER_CATEGORIES_ID, 'warning');
    }

    break;
}