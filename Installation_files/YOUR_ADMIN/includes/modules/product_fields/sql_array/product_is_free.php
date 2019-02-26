<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['product_is_free']) && $_POST['product_is_free'] != '') {
  $sql_data_array['product_is_free'] = (int)$_POST['product_is_free'];
}