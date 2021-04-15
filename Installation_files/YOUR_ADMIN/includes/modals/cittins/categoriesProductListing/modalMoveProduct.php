<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="moveProductModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="moveProductHeading"><?php echo TEXT_INFO_HEADING_MOVE_PRODUCT; ?></h4>
      </div>
      <div class="modal-body">
        <form name="formMoveProductConfirm" method="post" enctype="multipart/form-data" id="moveProductForm" class="form-horizontal">
          <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
          <?php echo zen_draw_hidden_field('products_id', '', 'id="moveProductId"'); ?>
          <?php echo zen_draw_hidden_field('current_category_id', $current_category_id); ?>
          <div class="form-group">
            <div class="col-sm-12">
              <p><?php echo TEXT_MOVE_PRODUCTS_INTRO; ?></p>
            </div>
          </div>
          <div id="moveProductNewCat" class="form-group">
            <?php echo zen_draw_label(TXT_LABEL_SELECT_NEW_CAT, 'move_to_category_id', 'class="controls-label col-sm-3"'); ?>
            <div class="col-sm-9">
              <?php echo zen_draw_pull_down_menu('move_to_category_id', zen_get_category_tree(), $current_category_id, 'id="moveToCategoryId" class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12">
              <label class="control-label col-sm-3"><?php echo TEXT_INFO_CURRENT_CATEGORIES; ?></label>
              <div class="col-sm-9">
                <p class="text-danger"><strong><?php echo TEXT_MASTER_CATEGORIES_ID; ?> ID#<span id="currentParentCatId"></span></strong></p>
                <p id="moveProdModalCurrentCat"></p>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12 text-center">
              <button type="submit" class="btn btn-danger" onclick="moveProductConfirm();"><?php echo IMAGE_MOVE; ?></button> <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo IMAGE_CANCEL; ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>