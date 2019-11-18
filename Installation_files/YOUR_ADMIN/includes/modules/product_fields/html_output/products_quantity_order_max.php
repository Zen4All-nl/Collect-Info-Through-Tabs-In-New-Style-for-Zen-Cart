<?php echo zen_draw_label(TEXT_PRODUCTS_QUANTITY_MAX_RETAIL, 'products_quantity_order_max', 'class="col-sm-3 control-label"'); ?> <i class="fa fa-lg fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo TEXT_PRODUCTS_QUANTITY_MAX_RETAIL_EDIT; ?>"></i>
<div class="col-sm-9 col-md-6">
  <?php echo zen_draw_input_field('products_quantity_order_max', $productInfo['products_quantity_order_max']['value'], 'class="form-control"'); ?>
</div>