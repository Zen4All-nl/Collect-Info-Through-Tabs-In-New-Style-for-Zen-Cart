<?php echo zen_draw_label(TEXT_PRODUCTS_VIRTUAL, 'products_virtual', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInfo['products_virtual']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_virtual" data-title="1"><?php echo TEXT_PRODUCT_IS_VIRTUAL; ?></a>
      <a class="btn btn-info <?php echo($productInfo['products_virtual']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_virtual" data-title="0"><?php echo TEXT_PRODUCT_NOT_VIRTUAL; ?></a>
      <?php echo ($productInfo['products_virtual']['value'] == '1' ? '<span class="alert">' . TEXT_VIRTUAL_EDIT . '</span>' : ''); ?>
    </div>
    <?php echo zen_draw_hidden_field('products_virtual', $productInfo['products_virtual']['value'], 'class="products_virtual"'); ?>
  </div>
</div>