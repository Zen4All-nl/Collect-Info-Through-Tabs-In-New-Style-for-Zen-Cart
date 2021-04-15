<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="deleteProductModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="deleteProductHeading"><?php echo TEXT_INFO_HEADING_DELETE_PRODUCT; ?></h4>
      </div>
      <div class="modal-body">
        <form name="formDeleteProductConfirm" method="post" enctype="multipart/form-data" id="deleteProductForm" class="form-horizontal">
          <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
          <div class="row">
            <?php echo zen_draw_hidden_field('products_id', '', 'id="deleteProductId"'); ?>
            <?php echo zen_draw_hidden_field('product_categories', '', 'id="deleteProductCategoryId"'); ?>
            <?php echo zen_draw_hidden_field('product_type', '', 'id="deleteProductType"'); ?>
            <div class="col-sm-12">
              <h3 id="delProdModalProdName"></h3>
            </div>
            <div class="col-sm-12">
              <p id="delProdModalMasterCat"></p>
            </div>
            <div class="col-sm-12">
              <p id="delProdModalIntro"></p>
            </div>
            <div class="col-sm-12">
              <p id="delProdModalCats"></p>
            </div>
            <div class="col-sm-12 text-center">
              <button type="submit" class="btn btn-danger" onclick="deleteProductConfirm();"><?php echo IMAGE_DELETE; ?></button> <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo IMAGE_CANCEL; ?></button>
            </div>
            <div class="row"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?></div>
            <div class="col-sm-12 text-center">
              <a id="delProdModalMultipleCatManagerLink" href="" class="btn btn-info" role="button"><?php echo BUTTON_PRODUCTS_TO_CATEGORIES; ?></a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
