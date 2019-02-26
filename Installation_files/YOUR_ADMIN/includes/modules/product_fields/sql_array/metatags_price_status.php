<?php

if (isset($_POST['metatags_price_status']) && $_POST['metatags_price_status'] != '') {
  $sql_ml_data_array['metatags_price_status'] = zen_db_prepare_input($_POST['metatags_price_status']);
}