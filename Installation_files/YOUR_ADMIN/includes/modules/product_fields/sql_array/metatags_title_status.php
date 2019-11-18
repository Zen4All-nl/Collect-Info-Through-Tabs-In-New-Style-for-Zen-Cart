<?php

if (isset($_POST['metatags_title_status']) && $_POST['metatags_title_status'] != '') {
  $sql_ml_data_array['metatags_title_status'] = zen_db_prepare_input($_POST['metatags_title_status']);
}