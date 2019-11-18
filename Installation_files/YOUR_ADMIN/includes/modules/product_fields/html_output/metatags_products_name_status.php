<?php
// metatags_products_name_status shows
if (empty($productInfo['metatags_keywords']['value']) && empty($productInfo['metatags_description']['value'])) {
  $productInfo['metatags_products_name_status']['value'] = zen_get_show_product_switch($productId, 'metatags_products_name_status');
}
?>
<div class="col-sm-12">
  <label class="control-label col-sm-4">
      <?php echo TEXT_META_TAG_TITLE_INCLUDES; ?>
  </label>
</div>
<?php echo zen_draw_label(TEXT_PRODUCTS_METATAGS_PRODUCTS_NAME_STATUS, 'metatags_products_name_status', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInfo['metatags_products_name_status']['value'] == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="1"><?php echo TEXT_YES; ?></a>
      <a class="btn btn-info <?php echo($productInfo['metatags_products_name_status']['value'] == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="0"><?php echo TEXT_NO; ?></a>
    </div>
    <?php echo zen_draw_hidden_field('metatags_products_name_status', ($productInfo['metatags_products_name_status']['value'] == true ? '1' : '0'), 'class="metatags_products_name_status"'); ?>
  </div>
</div>