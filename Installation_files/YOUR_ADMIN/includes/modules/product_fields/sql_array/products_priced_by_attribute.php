<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_priced_by_attribute']) && $_POST['products_priced_by_attribute'] != '') {
  $sql_data_array['products_priced_by_attribute'] = (int)$_POST['products_priced_by_attribute'];
}