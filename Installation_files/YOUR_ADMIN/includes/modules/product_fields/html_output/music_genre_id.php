<?php
$music_genre_array = array(array(
    'id' => '',
    'text' => TEXT_NONE));
$music_genres = $db->Execute("SELECT music_genre_id, music_genre_name
                              FROM " . TABLE_MUSIC_GENRE . "
                              ORDER BY music_genre_name");
foreach ($music_genres as $music_genre) {
  $music_genre_array[] = array(
    'id' => $music_genre['music_genre_id'],
    'text' => $music_genre['music_genre_name']);
}
?>
<?php echo zen_draw_label(TEXT_PRODUCTS_MUSIC_GENRE, 'music_genre_id', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_pull_down_menu('music_genre_id', $music_genre_array, $pInfo->music_genre_id, 'class="form-control"'); ?>
</div>