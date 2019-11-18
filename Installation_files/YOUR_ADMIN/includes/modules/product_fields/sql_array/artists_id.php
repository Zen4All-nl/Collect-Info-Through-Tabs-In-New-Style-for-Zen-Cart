<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['artists_id']) && $_POST['artists_id'] != '') {
  $sql_data_array['artists_id'] = (int)$_POST['artists_id'];
}