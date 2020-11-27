<?php
$manufacturers_array[] = [
  'id' => '',
  'text' => TEXT_NONE
];
$manufacturers = $db->Execute("SELECT manufacturers_id, manufacturers_name
                               FROM " . TABLE_MANUFACTURERS . "
                               ORDER BY manufacturers_name");
foreach ($manufacturers as $manufacturer) {
  $manufacturers_array[] = [
    'id' => $manufacturer['manufacturers_id'],
    'text' => $manufacturer['manufacturers_name']
  ];
}
?>
<?php echo zen_draw_label(TEXT_PRODUCTS_MANUFACTURER, 'manufacturers_id', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $productInformation->manufacturers_id['value'], 'class="form-control" id="manufacturers_id"'); ?>
</div>