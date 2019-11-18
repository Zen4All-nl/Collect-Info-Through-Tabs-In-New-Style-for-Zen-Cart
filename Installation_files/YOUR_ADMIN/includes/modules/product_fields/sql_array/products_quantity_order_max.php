<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_quantity_order_max']) && $_POST['products_quantity_order_max'] != '') {
  $sql_data_array['products_quantity_order_max'] = convertToFloat($_POST['products_quantity_order_max']);
}