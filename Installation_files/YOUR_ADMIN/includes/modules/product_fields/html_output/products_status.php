<?php
// set to out of stock if categories_status is off and new product or existing products_status is off
if (zen_get_categories_status($current_category_id) == '0' && $productInfo['products_status']['value'] != '1') {
  $productInfo['products_status']['value'] = 0;
}
?>
<?php echo zen_draw_label(TEXT_PRODUCTS_STATUS, 'products_status', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInfo['products_status']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_status" data-title="1"><?php echo TEXT_PRODUCT_AVAILABLE; ?></a>
      <a class="btn btn-info <?php echo($productInfo['products_status']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_status" data-title="0"><?php echo TEXT_PRODUCT_NOT_AVAILABLE; ?></a>
      <?php echo (zen_get_categories_status($current_category_id) == '0' ? TEXT_CATEGORIES_STATUS_INFO_OFF : '') . ($productInfo['products_status']['value'] == '0' ? ' ' . TEXT_PRODUCTS_STATUS_INFO_OFF : ''); ?>
    </div>
    <?php echo zen_draw_hidden_field('products_status', $productInfo['products_status']['value'], 'class="products_status"'); ?>
  </div>
</div>