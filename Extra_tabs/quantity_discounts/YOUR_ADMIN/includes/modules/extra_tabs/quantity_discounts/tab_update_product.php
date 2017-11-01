<?php

// insert or update quantity discounts

switch ($action) {
  case 'insert_product':
  case 'update_product':

    $db->Execute("delete from " . TABLE_PRODUCTS_DISCOUNT_QUANTITY . " where products_id='" . (int)$products_id . "'");
    $i = 1;
    $new_id = 0;
    $discount_cnt = 0;
    for ($i = 1, $n = sizeof($_POST['discount_qty']); $i <= $n; $i++) {
      if ($_POST['discount_qty'][$i] > 0) {
        $new_id++;
        $db->Execute("INSERT INTO " . TABLE_PRODUCTS_DISCOUNT_QUANTITY . " (discount_id, products_id, discount_qty, discount_price)
                      VALUES ('" . $new_id . "', '" . (int)$products_id . "', '" . zen_db_input($_POST['discount_qty'][$i]) . "', '" . zen_db_input($_POST['discount_price'][$i]) . "')");
        $discount_cnt++;
      } else {
        loop;
      }
    }

    if ($discount_cnt <= 0) {
      $db->Execute("UPDATE " . TABLE_PRODUCTS . "
                    SET products_discount_type = '0'
                    WHERE products_id = '" . (int)$products_id . "'");
    }
    break;
}