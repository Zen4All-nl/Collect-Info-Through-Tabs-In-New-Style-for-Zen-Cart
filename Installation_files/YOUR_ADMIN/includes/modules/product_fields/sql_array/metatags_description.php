<?php

if (isset($_POST['metatags_description']) && $_POST['metatags_description'] != '') {
  $sql_ml_data_array['metatags_description'] = zen_db_prepare_input($_POST['metatags_description']);
}