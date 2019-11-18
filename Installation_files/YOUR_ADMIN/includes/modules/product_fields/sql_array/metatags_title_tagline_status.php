<?php

if (isset($_POST['metatags_title_tagline_status']) && $_POST['metatags_title_tagline_status'] != '') {
  $sql_ml_data_array['metatags_title_tagline_status'] = zen_db_prepare_input($_POST['metatags_title_tagline_status']);
}