<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div id="setCategoryFlagModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="setFlagCategoriesHeading"><?php echo TEXT_INFO_HEADING_STATUS_CATEGORY; ?></h4><span id="setFlagCatIdHeading"></span>
      </div>
      <form name="formSetCategoryFlag" method="post" enctype="multipart/form-data" id="setCategoryFlagForm" class="form-horizontal">
        <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
        <?php echo zen_draw_hidden_field('categories_status', '', 'id="hiddenCategoriesStatus"'); ?>
        <?php echo zen_draw_hidden_field('categories_id', '', 'id="hiddenCategoriesId"'); ?>
        <?php echo zen_draw_hidden_field('cPath', '', 'id="hiddenCPath"'); ?>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">
              <p><?php echo TEXT_CATEGORIES_STATUS_INTRO; ?> <strong><?php ($cInfo->categories_status == '1' ? TEXT_CATEGORIES_STATUS_OFF : TEXT_CATEGORIES_STATUS_ON); ?></strong></p>
            </div>
            <div class="col-sm-12">
              <p><?php echo TEXT_CATEGORIES_STATUS_WARNING; ?></p>
              <div id="FlagRadioHasCategorySubcategories" class="form-group">
              </div>
              <div id="FlagRadioGetProductsToCategories" class="form-group">
              </div>
              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-primary"onclick="setCategoryFlagConfirm();"><?php echo IMAGE_UPDATE; ?></button> <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo IMAGE_CANCEL; ?></button>
              </div>
            </div>
          </div>
      </form>
    </div>
  </div>
</div>