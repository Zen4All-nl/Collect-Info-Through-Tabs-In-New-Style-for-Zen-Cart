<?php

if (isset($_POST['metatags_keywords']) && $_POST['metatags_keywords'] != '') {
  $sql_ml_data_array['metatags_keywords'] = zen_db_prepare_input($_POST['metatags_keywords']);
}