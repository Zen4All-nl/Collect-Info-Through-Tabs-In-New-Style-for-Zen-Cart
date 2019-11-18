<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_quantity_order_min']) && $_POST['products_quantity_order_min'] != '') {
  $sql_data_array['products_quantity_order_min'] = convertToFloat($_POST['products_quantity_order_min']) == 0 ? 1 : convertToFloat($_POST['products_quantity_order_min']);
}