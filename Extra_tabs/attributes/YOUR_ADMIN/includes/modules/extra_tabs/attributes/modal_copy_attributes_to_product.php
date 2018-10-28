<?php
// excluded current product from the pull down menu of products
$products_exclude_array = array();
$products_exclude_array[] = (int)$_GET['pID'];
?>
<div id="updateAttributesCopyToProduct" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <form name="productCopyToProduct" method="post" enctype="multipart/form-data" id="productCopyToProduct">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title"><?php echo TEXT_INFO_ATTRIBUTES_FEATURES_COPY_TO_PRODUCT . $_GET['pID'] . ' - ' . zen_get_products_name((int)$_GET['pID']); ?></h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <?php echo TEXT_COPY_ATTRIBUTES_CONDITIONS; ?>
          <div class="radio">
            <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_delete', true) . TEXT_COPY_ATTRIBUTES_DELETE; ?></label>
          </div>
          <div class="radio">
            <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_update') . ' ' . TEXT_COPY_ATTRIBUTES_UPDATE; ?></label>
          </div>
          <div class="radio">
            <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_ignore') . ' ' . TEXT_COPY_ATTRIBUTES_IGNORE; ?></label>
          </div>
        </div>
        <div class="form-group">
          <span class="alert">
            <?php echo zen_draw_label(TEXT_INFO_ATTRIBUTES_FEATURE_COPY_TO, 'products_update_id', 'class="control-label"'); ?>
          </span>
          <?php echo zen_draw_products_pull_down('products_update_id', 'size="15" class="form-control"', $products_exclude_array, true, '', true); ?>
        </div>
        <?php echo zen_draw_hidden_field('products_id', $_GET['pID']); ?>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" onclick="copyAttributesToProduct();"><i class="fa fa-copy"></i> <?php echo IMAGE_COPY; ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
      </div>
    </div>
  </div>
  </form>
</div>