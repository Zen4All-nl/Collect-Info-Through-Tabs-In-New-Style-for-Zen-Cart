<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_virtual']) && $_POST['products_virtual'] != '') {
  $sql_data_array['products_virtual'] = (int)$_POST['products_virtual'];
}