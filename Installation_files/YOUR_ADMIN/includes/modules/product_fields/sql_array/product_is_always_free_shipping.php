<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['product_is_always_free_shipping']) && $_POST['product_is_always_free_shipping'] != '') {
  $sql_data_array['product_is_always_free_shipping'] = (int)$_POST['product_is_always_free_shipping'];
}