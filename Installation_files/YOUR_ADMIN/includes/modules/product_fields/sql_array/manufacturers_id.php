<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['manufacturers_id']) && $_POST['manufacturers_id'] != '') {
  $sql_data_array['manufacturers_id'] = (int)$_POST['manufacturers_id'];
}