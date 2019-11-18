<?php echo zen_draw_label(TEXT_PRODUCTS_QTY_BOX_STATUS, 'products_qty_box_status', 'class="col-sm-3 control-label"'); ?>
<div class="col-sm-9 col-md-6">
  <div class="input-group">
    <div class="radioBtn btn-group">
      <a class="btn btn-info <?php echo($productInfo['products_qty_box_status']['value'] == '1' ? 'active' : 'notActive'); ?>" data-toggle="products_qty_box_status" data-title="1"><?php echo TEXT_PRODUCTS_QTY_BOX_STATUS_ON; ?></a>
      <a class="btn btn-info <?php echo($productInfo['products_qty_box_status']['value'] == '0' ? 'active' : 'notActive'); ?>" data-toggle="products_qty_box_status" data-title="0"><?php echo TEXT_PRODUCTS_QTY_BOX_STATUS_OFF; ?></a>
      <?php echo ($productInfo['products_qty_box_status']['value'] == '0' ? '<span class="alert">' . TEXT_PRODUCTS_QTY_BOX_STATUS_EDIT . '</span>' : ''); ?>
    </div>
    <?php echo zen_draw_hidden_field('products_qty_box_status', $productInfo['products_qty_box_status']['value'], 'class="products_qty_box_status"'); ?>
  </div>
</div>