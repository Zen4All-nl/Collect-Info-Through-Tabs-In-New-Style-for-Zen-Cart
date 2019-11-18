<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_weight']) && $_POST['products_weight'] != '') {
  $sql_data_array['products_weight'] = convertToFloat($_POST['products_weight']);
}