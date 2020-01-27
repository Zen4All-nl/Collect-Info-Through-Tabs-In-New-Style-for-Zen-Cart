<?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_MIN_RETAIL, 'products_quantity_order_min', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_input_field('products_quantity_order_min', $productInformation->products_quantity_order_min['value'], 'class="form-control" id="products_quantity_order_min"', '', 'number'); ?>
</div>