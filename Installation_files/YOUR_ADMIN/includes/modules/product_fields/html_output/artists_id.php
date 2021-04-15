<?php
$artists_array = [
  [
    'id' => '',
    'text' => TEXT_NONE
  ]
];
$artists = $db->Execute("SELECT artists_id, artists_name
                         FROM " . TABLE_RECORD_ARTISTS . "
                         ORDER BY artists_name");
foreach ($artists as $artist) {
  $artists_array[] = [
    'id' => $artist['artists_id'],
    'text' => $artist['artists_name']
  ];
}
?>
<?php echo zen_draw_label(TEXT_PRODUCTS_RECORD_ARTIST, 'artists_id', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_pull_down_menu('artists_id', $artists_array, $productInformation->artists_id['value'], 'class="form-control" id="artists_id"'); ?>
</div>