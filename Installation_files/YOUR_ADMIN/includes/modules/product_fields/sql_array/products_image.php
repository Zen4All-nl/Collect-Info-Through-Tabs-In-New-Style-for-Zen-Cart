<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['products_image']) && $_POST['products_image'] != '') {
  $db_filename = zen_limit_image_filename($_POST['products_image'], TABLE_PRODUCTS, 'products_image');
  $sql_data_array['products_image'] = zen_db_prepare_input($db_filename);
  $new_image = 'true';

  // when set to none remove from database
  // is out dated for browsers use radio only
  if ($_POST['image_delete'] == 1) {
    $sql_data_array['products_image'] = '';
    $new_image = 'false';
  }
}