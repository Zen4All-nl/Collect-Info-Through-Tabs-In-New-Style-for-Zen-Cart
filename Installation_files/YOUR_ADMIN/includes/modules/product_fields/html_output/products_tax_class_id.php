<?php
$tax_class_array[] = [
  'id' => '0',
  'text' => TEXT_NONE
];
$tax_class = $db->Execute("SELECT tax_class_id, tax_class_title
                           FROM " . TABLE_TAX_CLASS . "
                           ORDER BY tax_class_title");
foreach ($tax_class as $item) {
  $tax_class_array[] = [
    'id' => $item['tax_class_id'],
    'text' => $item['tax_class_title']
  ];
}
?>
<?php echo zen_draw_label(TEXT_PRODUCTS_TAX_CLASS, 'products_tax_class_id', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $productInformation->products_tax_class_id['value'], 'onchange="updateGross()" class="form-control" id="products_tax_class_id"'); ?>
</div>