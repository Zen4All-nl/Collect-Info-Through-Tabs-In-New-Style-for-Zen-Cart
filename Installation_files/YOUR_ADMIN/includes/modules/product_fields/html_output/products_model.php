<?php echo zen_draw_label(TEXT_PRODUCTS_MODEL, 'products_model', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_input_field('products_model', htmlspecialchars(stripslashes($productInformation->products_model['value']), ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_PRODUCTS, 'products_model') . ' class="form-control" id="products_model"'); ?>
</div>