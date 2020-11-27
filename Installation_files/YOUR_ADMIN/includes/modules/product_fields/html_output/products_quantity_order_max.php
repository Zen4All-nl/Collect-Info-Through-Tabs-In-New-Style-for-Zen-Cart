<?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_MAX_RETAIL, 'products_quantity_order_max', 'class="col-sm-3 control-label"'); ?> 
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <?php echo zen_draw_input_field('products_quantity_order_max', $productInformation->products_quantity_order_max['value'], 'class="form-control" id="products_quantity_order_max"', '', 'number'); ?>
    <span class="input-group-addon"><i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_PRODUCTS_QUANTITY_MAX_RETAIL_EDIT; ?>"></i></span>
  </div>
</div>