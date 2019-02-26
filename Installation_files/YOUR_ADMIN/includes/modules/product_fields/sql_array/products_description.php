<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_description']) && $_POST['products_description'] != '') {
  $sql_ml_data_array['products_description'] = zen_db_prepare_input($_POST['products_description']);
}