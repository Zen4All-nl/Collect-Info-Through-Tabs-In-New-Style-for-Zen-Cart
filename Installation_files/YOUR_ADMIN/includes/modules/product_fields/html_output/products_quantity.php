<?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY, 'products_quantity', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_input_field('products_quantity', htmlspecialchars(stripslashes($productInformation->products_quantity['value']), ENT_COMPAT, CHARSET, TRUE), 'class="form-control" id="products_quantity"'); ?>
</div>