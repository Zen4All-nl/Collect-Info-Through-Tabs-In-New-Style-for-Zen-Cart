<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_quantity_mixed']) && $_POST['products_quantity_mixed'] != '') {
  $sql_data_array['products_quantity_mixed'] = (int)$_POST['products_quantity_mixed'];
}