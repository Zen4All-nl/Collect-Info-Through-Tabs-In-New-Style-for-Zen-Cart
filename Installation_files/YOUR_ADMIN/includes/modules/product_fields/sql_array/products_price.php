<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_price']) && $_POST['products_price'] != '') {
  $sql_data_array['products_price'] = convertToFloat($_POST['products_price']);
}