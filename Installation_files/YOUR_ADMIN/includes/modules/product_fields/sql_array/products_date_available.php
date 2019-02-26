<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_date_available']) && $_POST['products_date_available'] != '') {
  $products_date_available_raw = zen_db_prepare_input($_POST['products_date_available']);
  $products_date_available = (date('Y-m-d') < $products_date_available_raw) ? $products_date_available_raw : 'null';
  $sql_data_array['products_date_available'] = zen_db_prepare_input($_POST['products_date_available']);
}