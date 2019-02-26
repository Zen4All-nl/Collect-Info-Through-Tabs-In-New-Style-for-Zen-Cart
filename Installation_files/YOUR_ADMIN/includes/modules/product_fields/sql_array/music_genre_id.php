<?php

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if (isset($_POST['music_genre_id']) && $_POST['music_genre_id'] != '') {
  $sql_data_array['music_genre_id'] = (int)$_POST['music_genre_id'];
}