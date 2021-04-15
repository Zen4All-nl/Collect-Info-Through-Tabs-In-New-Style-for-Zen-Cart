<?php
// metatags_products_name_status shows
if (empty($productInformation->metatags_keywords['value']) && empty($productInformation->metatags_description['value'])) {
  $productInformation->metatags_products_name_status['value'] = zen_get_show_product_switch($productId, 'metatags_products_name_status');
}
?>
<div class="col-sm-12">
  <p><?php echo TEXT_META_TAG_TITLE_INCLUDES; ?></p>
</div>
</div>
<div class="form-group">
  <p class="col-sm-3 control-label"><?php echo TEXT_PRODUCTS_METATAGS_PRODUCTS_NAME_STATUS; ?></p>
  <div class="col-sm-9 col-md-6">
    <div class="input-group">
      <div class="radioBtn btn-group">
        <a class="btn btn-info <?php echo($productInformation->metatags_products_name_status['value'] == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="1"><?php echo TEXT_YES; ?></a>
        <a class="btn btn-info <?php echo($productInformation->metatags_products_name_status['value'] == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_products_name_status" data-title="0"><?php echo TEXT_NO; ?></a>
      </div>
      <?php echo zen_draw_hidden_field('metatags_products_name_status', ($productInformation->metatags_products_name_status['value'] == true ? '1' : '0'), 'class="metatags_products_name_status"'); ?>
    </div>
  </div>