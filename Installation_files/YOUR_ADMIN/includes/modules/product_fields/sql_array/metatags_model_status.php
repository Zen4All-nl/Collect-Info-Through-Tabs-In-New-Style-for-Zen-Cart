<?php

if (isset($_POST['metatags_model_status']) && $_POST['metatags_model_status'] != '') {
  $sql_ml_data_array['metatags_model_status'] = zen_db_prepare_input($_POST['metatags_model_status']);
}