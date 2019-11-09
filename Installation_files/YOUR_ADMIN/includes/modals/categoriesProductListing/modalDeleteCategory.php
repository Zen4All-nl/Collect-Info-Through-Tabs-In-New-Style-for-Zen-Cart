<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="deleteCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="deleteCategoryHeading"><?php echo TEXT_INFO_HEADING_DELETE_CATEGORY; ?></h4>
      </div>
      <form name="formDeleteCategoryConfirm" action="<?php echo zen_href_link(FILENAME_ZEN4ALL_CATEGORIES_PRODUCT_LISTING, 'action=delete_category_confirm'); ?>" method="post" enctype="multipart/form-data" id="deleteCategoryForm" class="form-horizontal">
        <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
        <div class="modal-body">
          <div class="row">
            <?php echo zen_draw_hidden_field('cPath', '', 'id="cPath"'); ?>
            <?php echo zen_draw_hidden_field('categories_id', '', 'id="categoryId"'); ?>
            <div class="col-sm-12">
              <p><strong><?php echo TEXT_DELETE_CATEGORY_INTRO; ?></strong></p>
            </div>
            <div class="col-sm-12">
              <p><strong><?php echo TEXT_DELETE_CATEGORY_INTRO_LINKED_PRODUCTS; ?></strong></p>
            </div>
            <div class="col-sm-12">
              <p id="delCatModalCatName"></p>
            </div>
            <div class="col-sm-12">
              <p id="childs_count">
                <?php echo TEXT_DELETE_WARNING_CHILDS_START; ?>&nbsp;<span id="childs_count_number"></span>&nbsp;<?php echo TEXT_DELETE_WARNING_CHILDS_END; ?>
              </p>
            </div>
            <div class="col-sm-12">
              <p id="products_count">
                <?php echo TEXT_DELETE_WARNING_PRODUCTS_START; ?>&nbsp;<span id="products_count_number"></span>&nbsp;<?php echo TEXT_DELETE_WARNING_PRODUCTS_END; ?>
              </p>
            </div>
            <?php
            /*
              // future cat specific
              if ($cInfo->products_count > 0) {
              $contents[] = array('text' => TEXT_PRODUCTS_LINKED_INFO . '<br>' .
              zen_draw_radio_field('delete_linked', '1') . ' ' . TEXT_PRODUCTS_DELETE_LINKED_YES . '<br>' .
              zen_draw_radio_field('delete_linked', '0', true) . ' ' . TEXT_PRODUCTS_DELETE_LINKED_NO);
              }
             */
            ?>
            <div class="col-sm-12 text-center">
              <button type="submit" class="btn btn-danger" onclick="deletCategoryConfirm();"><?php echo IMAGE_DELETE; ?></button> <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo IMAGE_CANCEL; ?></button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>