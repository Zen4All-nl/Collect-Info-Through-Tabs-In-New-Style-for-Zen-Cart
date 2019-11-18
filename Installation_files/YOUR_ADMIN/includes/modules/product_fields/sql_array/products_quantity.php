<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_quantity']) && $_POST['products_quantity'] != '') {
  $sql_data_array['products_quantity'] = convertToFloat($_POST['products_quantity']);
}