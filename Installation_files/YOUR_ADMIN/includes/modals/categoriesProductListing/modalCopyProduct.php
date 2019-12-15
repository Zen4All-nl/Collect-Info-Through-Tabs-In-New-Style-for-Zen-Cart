<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="copyProductModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="copyProductHeading"><?php echo TEXT_INFO_HEADING_COPY_TO; ?></h4>
      </div>
      <form name="formCopyProductConfirm" method="post" enctype="multipart/form-data" id="copyProductForm" class="form-horizontal">
        <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
        <div class="modal-body">
          <?php echo zen_draw_hidden_field('products_id', '', 'id="copyProductId"'); ?>
          <?php echo zen_draw_hidden_field('product_type', '', 'id="copyProductType"'); ?>
          <div class="form-group">
            <label class="control-label col-sm-3"><?php echo TEXT_INFO_CURRENT_PRODUCT; ?></label>
            <div class="col-sm-9"><strong><span id="copyProductCurrentInfo"></span></strong></div>
          </div>
          <div id="copyProductNewCat" class="form-group">
            <?php echo zen_draw_label(TXT_LABEL_SELECT_NEW_CAT, 'categories_id', 'class="control-label col-sm-3"'); ?>
            <div class="col-sm-9">
              <?php echo zen_draw_pull_down_menu('categories_id', zen_get_category_tree(), $current_category_id, 'id="copyToCategoryId" class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-3"><?php echo TEXT_INFO_CURRENT_CATEGORIES; ?></label>
            <div id="copyProdModalCurrentCat" class="col-sm-9"></div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-3"><?php echo TEXT_HOW_TO_COPY; ?></label>
            <div class="col-sm-9">
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_as', 'link', true) . TEXT_COPY_AS_LINK; ?></label>
              </div>
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_as', 'duplicate') . TEXT_COPY_AS_DUPLICATE; ?></label>
              </div>
            </div>
          </div>
          <div id="copyProductModalAttributes" class="form-group">
            <label class="control-label col-sm-3"><?php echo TEXT_COPY_ATTRIBUTES; ?></label>
            <div class="col-sm-9">
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_yes', true) . TEXT_YES; ?></label>
              </div>
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_attributes', 'copy_attributes_no') . TEXT_NO; ?></label>
              </div>
            </div>
          </div>
          <div id="copyProductModalMetaTags" class="form-group">
            <label class="control-label col-sm-3"><?php echo TEXT_COPY_METATAGS; ?></label>
            <div class="col-sm-9">
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_metatags', 'copy_metatags_yes', true) . TEXT_YES; ?></label>
              </div>
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_metatags', 'copy_metatags_no') . TEXT_NO; ?></label>
              </div>
            </div>
          </div>
          <div id="copyProductModalLinked" class="form-group">
            <label class="control-label col-sm-3"><?php echo TEXT_COPY_LINKED_CATEGORIES; ?></label>
            <div class="col-sm-9">
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_linked_categories', 'copy_linked_categories_yes', true) . TEXT_YES; ?></label>
              </div>
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_linked_categories', 'copy_linked_categories_no') . TEXT_NO; ?></label>
              </div>
            </div>
          </div>
          <div id="copyProductModalDiscounts" class="form-group">
            <label class="control-label col-sm-3"><?php echo TEXT_COPY_DISCOUNTS; ?></label>
            <div class="col-sm-9">
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_discounts', 'copy_discounts_yes', true) . TEXT_YES; ?></label>
              </div>
              <div class="radio">
                <label><?php echo zen_draw_radio_field('copy_discounts', 'copy_discounts_no') . TEXT_NO; ?></label>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12 text-center">
              <button type="submit" class="btn btn-danger" onclick="moveProductConfirm();"><?php echo IMAGE_MOVE; ?></button> <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo IMAGE_CANCEL; ?></button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>