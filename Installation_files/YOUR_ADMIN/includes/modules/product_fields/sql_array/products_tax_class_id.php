<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_tax_class_id']) && $_POST['products_tax_class_id'] != '') {
  $sql_data_array['products_tax_class_id'] = (int)$_POST['products_tax_class_id'];
}