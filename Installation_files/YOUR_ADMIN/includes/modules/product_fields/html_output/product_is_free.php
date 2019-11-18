<?php echo zen_draw_label(TEXT_PRODUCT_IS_FREE, 'product_is_free', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInfo['product_is_free']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="product_is_free" data-title="1"><?php echo TEXT_YES; ?></a>
      <a class="btn btn-info <?php echo($productInfo['product_is_free']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="product_is_free" data-title="0"><?php echo TEXT_NO; ?></a>
      <?php echo ($productInfo['product_is_free']['value'] == '1' ? '<span class="alert">' . TEXT_PRODUCTS_IS_FREE_EDIT . '</span>' : ''); ?>
    </div>
    <?php echo zen_draw_hidden_field('product_is_free', $productInfo['product_is_free']['value'], 'class="product_is_free"'); ?>
  </div>
</div>