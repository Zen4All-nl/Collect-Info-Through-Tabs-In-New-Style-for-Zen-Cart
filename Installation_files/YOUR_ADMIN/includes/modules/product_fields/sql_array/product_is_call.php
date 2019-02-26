<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['product_is_call']) && $_POST['product_is_call'] != '') {
  $sql_data_array['product_is_call'] = (int)$_POST['product_is_call'];
}