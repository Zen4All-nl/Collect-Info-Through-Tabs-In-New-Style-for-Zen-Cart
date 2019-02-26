<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_quantity_order_units']) && $_POST['products_quantity_order_units'] != '') {
  $sql_data_array['products_quantity_order_units'] = convertToFloat($_POST['products_quantity_order_units']) == 0 ? 1 : convertToFloat($_POST['products_quantity_order_units']);
}