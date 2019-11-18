<?php

if (isset($_POST['metatags_title']) && $_POST['metatags_title'] != '') {
  $sql_ml_data_array['metatags_title'] = zen_db_prepare_input($_POST['metatags_title']);
}