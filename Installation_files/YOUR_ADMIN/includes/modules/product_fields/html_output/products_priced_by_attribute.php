<?php echo zen_draw_label(TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES, 'products_priced_by_attribute', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInfo['products_priced_by_attribute']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_priced_by_attribute" data-title="1"><?php echo TEXT_PRODUCT_IS_PRICED_BY_ATTRIBUTE; ?></a>
      <a class="btn btn-info <?php echo($productInfo['products_priced_by_attribute']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_priced_by_attribute" data-title="0"><?php echo TEXT_PRODUCT_NOT_PRICED_BY_ATTRIBUTE; ?></a>
      <?php echo ($productInfo['products_priced_by_attribute']['value'] == '1' ? '<span class="alert">' . TEXT_PRODUCTS_PRICED_BY_ATTRIBUTES_EDIT . '</span>' : ''); ?>
    </div>
    <?php echo zen_draw_hidden_field('products_priced_by_attribute', $productInfo['products_priced_by_attribute']['value'], 'class="products_priced_by_attribute"'); ?>
  </div>
</div>