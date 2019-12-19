<div class="row">
  <div class="col-sm-4 text-center" id="mainImage">
    <div class="panel panel-info">
      <div class="panel-heading"><?php echo TEXT_PRODUCTS_IMAGE; ?></div>
      <div class="panel-body">
        <div class="col-sm-8">
          <div id="mainImageThumb" data-toggle="modal" data-target="#imagePreviewModal" role="button">
            <?php if ($productInfo['products_image']['value'] != '') { ?>
              <?php echo zen_image(DIR_WS_CATALOG_IMAGES . $productInfo['products_image']['value'], '', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="img-thumbnail" id="mainImage"'); ?>
              <br>
              <?php echo TEXT_CLICK_TO_ENLARGE; ?>
            <?php } else { ?>
              <?php echo NONE; ?>
            <?php } ?>
          </div>
          <?php echo zen_draw_hidden_field('products_image', $productInfo['products_image']['value'], 'id="mainProductImage"'); ?>
          <?php echo zen_draw_hidden_field('products_previous_image', $productInfo['products_image']['value']); ?>
          <div id="mainImagePath">
            <?php echo ($productInfo['products_image']['value'] != '' ? $productInfo['products_image']['value'] : NONE); ?></div>
        </div>
        <div class="col-sm-4">
          <div role="group">
            <?php if ($productInfo['products_image']['value'] != '') { ?>
              <button type="button" id="button-edit-main-image" class="btn btn-primary" data-original-title="<?php echo TEXT_CHANGE_IMAGE; ?>" data-toggle="modal" data-target="#mainImageEditModal"><i class="fa fa-pencil" aria-hidden="true"></i></button>
            <?php } else { ?>
              <button type="button" id="button-add-main-image" class="btn btn-primary" data-original-title="<?php echo TEXT_ADD_IMAGE; ?>" data-toggle="modal" data-target="#mainImageEditModal"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
            <?php } ?>
              <button type="button" id="button-delete-main-image" class="btn btn-danger" data-original-title="<?php ?>" data-toggle="modal" data-target="#mainImageDeleteModal"<?php echo($productInfo['products_image']['value'] == '' ? 'style="display: none"' : ''); ?>><i class="fa fa-trash-o" aria-hidden="true"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /* BOF future code */ -->
<?php
if ($productInfo['products_image']['value'] != '' && $productInfo['products_id']['value'] != '') {
  $additionalImages = getAdditionalImages($productInfo['products_id']['value'], $productInfo['products_image']['value']);
} // if products_image
?>
<div class="row">
  <div class="panel panel-primary" id="additionalImages"<?php echo (($productInfo['products_image']['value'] != '' && $productInfo['products_id']['value'] != '') ? '' : ' style="display:none"') ?>>
    <div class="panel-heading"><?php echo TEXT_ADDITIONAL_IMAGES; ?></div>
    <div class="panel-body">
      <?php
      if (is_array($additionalImages['images'])) {
        foreach ($additionalImages['images'] as $image) {
          if (isset($image['count'])) {
            ?>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 text-center">
              <div class="panel panel-info">
                <div class="panel-body">
                  <div class="col-sm-8">
                    <div <?php echo 'id="additionalImageThumb-' . $image['suffix_number'] . '"'; ?> data-toggle="modal" <?php echo 'data-target="#imagePreviewModal-' . $image['suffix_number'] . '"'; ?> role="button">
                      <?php echo zen_image($image['filepath'], '', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="img-thumbnail" id="additionalImage-' . $image['suffix_number'] . '"'); ?>
                      <br/>
                      <?php echo TEXT_CLICK_TO_ENLARGE; ?>
                    </div>
                    <div <?php echo 'id="additionalImagePath-' . $image['suffix_number'] . '"'; ?>>
                      <?php echo $image['filename']; ?></div>
                  </div>
                  <div class="col-sm-4">
                    <div role="group">
                      <button type="button" <?php echo 'id="button-edit-additional-image-' . $image['suffix_number'] . '"'; ?> class="btn btn-primary" data-original-title="<?php echo TEXT_CHANGE_IMAGE; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                      <button type="button" <?php echo 'id="button-delete-additional-image-' . $image['suffix_number'] . '"'; ?> class="btn btn-danger" data-original-title="<?php ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
        }
      }
      ?>
    </div>
    <div class="panel-footer">
      <button type="button" id="button-add-additional-image-1" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
    </div>
  </div>
</div>
<script>
  $('#mainImageEditModal').on('click', '.radioBtn a', function () {
    var sel = $(this).data('title');
    var tog = $(this).data('toggle');
    $(this).parent().next('.' + tog).prop('value', sel);
    $(this).parent().find('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
    $(this).parent().find('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
  });
</script>