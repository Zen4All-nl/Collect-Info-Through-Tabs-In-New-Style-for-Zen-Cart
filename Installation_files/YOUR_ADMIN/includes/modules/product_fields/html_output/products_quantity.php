<?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY, 'products_quantity', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_input_field('products_quantity', htmlspecialchars(stripslashes($productInfo['products_quantity']['value']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS, 'products_quantity') . ' class="form-control"'); ?>
</div>