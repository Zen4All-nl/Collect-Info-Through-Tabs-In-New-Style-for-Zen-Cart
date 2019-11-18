<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_model']) && $_POST['products_model'] != '') {
  $sql_data_array['products_model'] = zen_db_prepare_input($_POST['products_model']);
}