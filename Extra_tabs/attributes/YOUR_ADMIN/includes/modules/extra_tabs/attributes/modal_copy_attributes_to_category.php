
<div id="updateAttributesCopyToCategory" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <?php echo zen_draw_form('product_copy_to_category', FILENAME_ATTRIBUTES_CONTROLLER, 'action=update_attributes_copy_to_category'); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title"><?php echo TEXT_INFO_ATTRIBUTES_FEATURES_COPY_TO_CATEGORY . $products_filter . '<br />' . zen_get_products_name($products_filter); ?></h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <?php echo TEXT_COPY_ATTRIBUTES_CONDITIONS; ?>
          <div class="radio">
            <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_delete', true) . TEXT_COPY_ATTRIBUTES_DELETE; ?></label>
          </div>
          <div class="radio">
            <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_update') . TEXT_COPY_ATTRIBUTES_UPDATE; ?></label>
          </div>
          <div class="radio">
            <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_ignore') . TEXT_COPY_ATTRIBUTES_IGNORE; ?></label>
          </div>
        </div>
        <div class="form-group">
          <span class="alert">
            <?php echo zen_draw_label(TEXT_INFO_ATTRIBUTES_FEATURE_CATEGORIES_COPY_TO, 'categories_update_id', 'class="control-label"'); ?>
          </span>
          <?php echo zen_draw_products_pull_down_categories('categories_update_id', 'size="5" class="form-control" id="categories_update_id"', '', true, true); ?>
        </div>
        <?php echo zen_draw_hidden_field('products_filter', $_GET['pID']); ?>
        <?php echo zen_draw_hidden_field('products_update_id', $_GET['products_update_id']); ?>
        <?php echo zen_draw_hidden_field('view', 'productCopyToCategory'); ?>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa fa-copy"></i> <?php echo IMAGE_COPY; ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
      </div>
    </div>
  </div>
  <?php echo '</form>'; ?>
</div>