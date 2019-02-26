<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_name']) && $_POST['products_name'] != '') {
  $sql_ml_data_array['products_name'] = zen_db_prepare_input($_POST['products_name']);
}