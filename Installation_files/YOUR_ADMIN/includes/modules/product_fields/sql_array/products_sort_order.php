<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_sort_order']) && $_POST['products_sort_order'] != '') {
  $sql_data_array['products_sort_order'] = (int)$_POST['products_sort_order'];
}