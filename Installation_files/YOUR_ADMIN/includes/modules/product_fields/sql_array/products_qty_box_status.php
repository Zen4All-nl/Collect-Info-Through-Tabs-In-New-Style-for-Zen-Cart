<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_qty_box_status']) && $_POST['products_qty_box_status'] != '') {
  $sql_data_array['products_qty_box_status'] = (int)$_POST['products_qty_box_status'];
}