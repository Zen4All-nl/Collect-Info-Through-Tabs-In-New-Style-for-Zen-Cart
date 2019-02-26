<?php echo zen_draw_label(TEXT_PRODUCTS_MIXED, 'products_quantity_mixed', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInfo['products_quantity_mixed']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_quantity_mixed" data-title="1"><?php echo TEXT_YES; ?></a>
      <a class="btn btn-info <?php echo($productInfo['products_quantity_mixed']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_quantity_mixed" data-title="0"><?php echo TEXT_NO; ?></a>
    </div>
    <?php echo zen_draw_hidden_field('products_quantity_mixed', $productInfo['products_quantity_mixed']['value'], 'class="products_quantity_mixed"'); ?>
  </div>
</div>