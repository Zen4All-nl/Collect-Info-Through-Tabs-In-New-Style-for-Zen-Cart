<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="moveCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4><?php echo TEXT_INFO_HEADING_MOVE_CATEGORY; ?></h4>
      </div>
      <div class="modal-body">
        <form name="formMoveCategoryConfirm" action=""<?php echo zen_href_link(FILENAME_CITTINS_CATEGORIES_PRODUCT_LISTING); ?>" method="post" enctype="multipart/form-data" id="moveCategoryForm" class="form-horizontal">
          <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
          <?php echo zen_draw_hidden_field('cPath', '', 'id="cPath"'); ?>
          <?php echo zen_draw_hidden_field('categories_id', '', 'id="moveCategoryId"'); ?>
          <div class="form-group">
            <div class="col-sm-12">
              <p><strong><?php echo TEXT_MOVE_CATEGORIES_INTRO_START; ?>&nbsp;<span class="category_name"></span>&nbsp;<?php echo TEXT_MOVE_CATEGORIES_INTRO_END; ?></strong></p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12">
              <?php echo zen_draw_label('<span class="category_name"></span>&nbsp;' . TEXT_MOVE_CATEGORY, 'move_to_category_id', 'id="label_moveToCategoryId"') . zen_draw_pull_down_menu('move_to_category_id', zen_get_category_tree(), '', 'class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12 text-center">
              <button type="submit" class="btn btn-danger" onclick="moveCategoryConfirm();"><?php echo IMAGE_MOVE; ?></button> <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo IMAGE_CANCEL; ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>