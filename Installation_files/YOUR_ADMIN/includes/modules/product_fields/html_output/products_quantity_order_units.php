<?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_UNITS_RETAIL, 'products_quantity_order_units', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_input_field('products_quantity_order_units', $productInformation->products_quantity_order_units['value'], 'class="form-control" id="products_quantity_order_units"', '', 'number'); ?>
</div>