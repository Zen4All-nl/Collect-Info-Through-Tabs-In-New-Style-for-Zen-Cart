<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_status']) && $_POST['products_status'] != '') {
  $sql_data_array['products_status'] = (int)$_POST['products_status'];
}