<!-- Delete Option modal-->
<div id="deleteOptionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="deleteOptionModalLabel"><?php echo TITLE_CONFIRM_DELETE; ?></h4>
      </div>
      <form name="delete_option" method="post" enctype="multipart/form-data" id="deleteOptionConfirm">
        <div class="modal-body bg-danger" id="deleteOptionText">
          <p class="danger"><?php echo TEXT_DELETE_ATTRIBUTES_OPTION_NAME_VALUES; ?></p>
          <div class="form-group">
            <p class="form-control"><?php echo TEXT_INFO_PRODUCT_NAME . zen_get_products_name($_GET['pID'], $_SESSION['languages_id']); ?></p>
            <p class="form-control" id="deleteOptionName"></p>
            <p class="form-control" id="deleteOptionId"></p>
          </div>
          <?php echo zen_draw_hidden_field('options_id', ''); ?>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger" onclick="deleteOption()"><i class="fa fa-trash"></i></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> <?php echo TEXT_CLOSE; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>