<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['record_company_id']) && $_POST['record_company_id'] != '') {
  $sql_data_array['record_company_id'] = (int)$_POST['record_company_id'];
}