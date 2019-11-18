<?php echo zen_draw_label(TEXT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING, 'product_is_always_free_shipping', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info btn-sm <?php echo($productInfo['product_is_always_free_shipping']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="product_is_always_free_shipping" data-title="1"><?php echo TEXT_PRODUCT_IS_ALWAYS_FREE_SHIPPING; ?></a>
      <a class="btn btn-info btn-sm <?php echo($productInfo['product_is_always_free_shipping']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="product_is_always_free_shipping" data-title="0"><?php echo TEXT_PRODUCT_NOT_ALWAYS_FREE_SHIPPING; ?></a>
      <a class="btn btn-info btn-sm <?php echo($productInfo['product_is_always_free_shipping']['value'] == '2' ? 'active' : 'notActive'); ?>" data-toggle="product_is_always_free_shipping" data-title="2"><?php echo TEXT_PRODUCT_SPECIAL_ALWAYS_FREE_SHIPPING; ?></a>
    </div>
    <?php echo zen_draw_hidden_field('product_is_always_free_shipping', $productInfo['product_is_always_free_shipping']['value'], 'class="product_is_always_free_shipping"'); ?>
  </div>
</div>