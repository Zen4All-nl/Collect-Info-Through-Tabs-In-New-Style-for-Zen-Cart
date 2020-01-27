<?php
// metatags_products_name_status shows
if (empty($productInformation->metatags_keywords['value']) && empty($productInformation->metatags_description['value'])) {
  $productInformation->metatags_model_status['value'] = zen_get_show_product_switch($productId, 'metatags_model_status');
}
?>
<p class="col-sm-3 control-label"><?php echo TEXT_PRODUCTS_METATAGS_MODEL_STATUS; ?></p>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInformation->metatags_model_status['value'] == true ? 'active' : 'notActive'); ?>" data-toggle="metatags_model_status" data-title="1"><?php echo TEXT_YES; ?></a>
      <a class="btn btn-info <?php echo($productInformation->metatags_model_status['value'] == false ? 'active' : 'notActive'); ?>" data-toggle="metatags_model_status" data-title="0"><?php echo TEXT_NO; ?></a>
    </div>
    <?php echo zen_draw_hidden_field('metatags_model_status', ($productInformation->metatags_model_status['value'] == true ? '1' : '0'), 'class="metatags_model_status"'); ?>
  </div>
</div>