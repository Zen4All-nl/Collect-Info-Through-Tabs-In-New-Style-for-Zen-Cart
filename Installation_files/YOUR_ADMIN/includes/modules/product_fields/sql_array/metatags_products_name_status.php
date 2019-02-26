<?php

if (isset($_POST['metatags_products_name_status']) && $_POST['metatags_products_name_status'] != '') {
  $sql_ml_data_array['metatags_products_name_status'] = zen_db_prepare_input($_POST['metatags_products_name_status']);
}