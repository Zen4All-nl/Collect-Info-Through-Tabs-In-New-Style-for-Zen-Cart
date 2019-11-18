<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_url']) && $_POST['products_url'] != '') {
  $sql_ml_data_array['products_url'] = zen_db_prepare_input($_POST['products_url']);
}