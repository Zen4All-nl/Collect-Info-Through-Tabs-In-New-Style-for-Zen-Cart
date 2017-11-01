<!-- remove all attributes from the product -->

<div id="deleteAllAttributes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <?php echo zen_draw_form('delete_all', FILENAME_ATTRIBUTES_CONTROLLER, 'action=delete_all_attributes'); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
      </div>
      <div class="modal-body bg-danger">
        <div class="form-group">
          <?php echo TEXT_DELETE_ALL_ATTRIBUTES . $_GET['pID'] . ' - ' . zen_get_products_name((int)$_GET['pID']); ?>
          <?php echo zen_draw_hidden_field('products_id', (int)$_GET['pID']); ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa fa-trash"></i> <?php echo IMAGE_DELETE; ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
      </div>
    </div>
  </div>
  <?php echo '</form>'; ?>
</div>