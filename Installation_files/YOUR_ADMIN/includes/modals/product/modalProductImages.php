<?php
/* Creates the bootstrap modal where the image will appear */

list($imageWidth, $imageHeight) = getimagesize(DIR_FS_CATALOG_IMAGES . $productInfo['products_image']['value']);
$mediumWith = (int)MEDIUM_IMAGE_WIDTH;
if ($imageWidth > $mediumWith) {
  $width = $mediumWith;
} else {
  $width = $imageWidth;
}
if ($height > MEDIUM_IMAGE_HEIGHT) {
  $height = MEDIUM_IMAGE_HEIGHT;
}
?>
<!-- Product main image preview modal-->
<div id="imagePreviewModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i><span class="sr-only"><?php echo TEXT_CLOSE; ?></span></button>
        <h4 class="modal-title" id="imagePreviewModalLabel"><?php echo IMAGE_PREVIEW; ?></h4>
      </div>
      <div class="modal-body text-center" id="mainImageLarger">
        <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $productInfo['products_image']['value'], '', $width) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo TEXT_CLOSE; ?></button>
      </div>
    </div>
  </div>
</div>
<!-- Product main image delete modal-->
<div id="mainImageDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i><span class="sr-only"><?php echo TEXT_CLOSE; ?></span></button>
        <h4 class="modal-title" id="mainImageDeleteModalLabel"><?php echo IMAGE_DELETE; ?></h4>
      </div>
      <div class="modal-body">
        <p><?php echo TEXT_IMAGES_DELETE_NOTE; ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="deleteMainImage();"><i class="fa fa-trash"></i></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo TEXT_CLOSE; ?></button>
      </div>
    </div>
  </div>
</div>
<!-- Product main image add/edit modal-->
<?php
$dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
$default_directory = substr($productInfo['products_image']['value'], 0, strpos($productInfo['products_image']['value'], DIRECTORY_SEPARATOR) + 1);
?>
<div id="mainImageEditModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i>
          <span class="sr-only"><?php echo TEXT_CLOSE; ?></span>
        </button>
        <h4 class="modal-title" id="imageModalLabel">Image Edit</h4>
      </div>
      <form name="mainImageSelect" method="post" enctype="multipart/form-data" id="mainImageSelect" class="form-horizontal">
        <?php echo zen_draw_hidden_field('securityToken', $_SESSION['securityToken']); ?>
        <div class="modal-body">
          <div class="form-group">
            <div class="col-sm-12">
              <?php echo zen_draw_file_field('products_image', '', 'id="fileField" class="form-control" accept="image/*"'); ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12">
              <?php echo zen_draw_label(TEXT_PRODUCTS_IMAGE_DIR, 'img_dir', 'class="control-label"'); ?>
              <?php echo zen_draw_pull_down_menu('img_dir', $dir_info, $default_directory, 'id="image_dir" class="form-control"'); ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-12">
              <?php echo zen_draw_label(TEXT_IMAGES_OVERWRITE, 'overwrite', 'class="control-label"'); ?>
              <div class="input-group">
                <div class="radioBtn btn-group">
                  <a class="btn btn-info notActive" data-toggle="overwrite" data-title="0"><?php echo TABLE_HEADING_NO; ?></a>
                  <a class="btn btn-info active" data-toggle="overwrite" data-title="1"><?php echo TABLE_HEADING_YES; ?></a>
                </div>
              </div>
              <?php echo zen_draw_hidden_field('overwrite', '1', 'class="overwrite"'); ?>
            </div>
          </div>
          <?php if ($productInfo['products_image']['value'] != '') { ?>
            <div class="form-group">
              <div class="col-sm-12">
                <?php echo zen_draw_label(TEXT_RENAME_ADDITIONAL_IMAGES, 'rename', 'class="control-label"'); ?>
                <div class="input-group">
                  <div class="radioBtn btn-group">
                    <a class="btn btn-info notActive" data-toggle="rename" data-title="0"><?php echo TABLE_HEADING_NO; ?></a>
                    <a class="btn btn-info active" data-toggle="rename" data-title="1"><?php echo TABLE_HEADING_YES; ?></a>
                  </div>
                </div>
                <?php echo zen_draw_hidden_field('rename', '1', 'class="rename"'); ?>
              </div>
            </div>
          <?php } ?>
          <hr style="border-top: 1px solid #8c8b8b">
          <div class="form-group">
            <div class="col-sm-12">
              <?php echo zen_draw_label(TEXT_PRODUCTS_IMAGE_MANUAL, 'products_image_manual', 'class="control-label"'); ?>
              <?php echo zen_draw_input_field('products_image_manual', '', 'class="form-control"'); ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <?php echo zen_draw_hidden_field('products_previous_image', $productInfo['products_image']['value']); ?>
          <?php echo zen_draw_hidden_field('productId', $productId); ?>
          <?php echo zen_draw_hidden_field('current_category_id', $current_category_id); ?>
          <button type="submit" class="btn btn-primary" onclick="saveMainImage();"><i class="fa fa-save"></i></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> <?php echo TEXT_CLOSE; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>