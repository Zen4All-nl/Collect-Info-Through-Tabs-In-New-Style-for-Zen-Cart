<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script>
  // script for tooltips
  $(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
  // script for preview popup
  $('#previewPopUp').on('click', (function (e) {
    e.preventDefault();
    $('#previewmodal').modal('show');
  }));
  // script for sliding checkbox
  $(document).ready(function () {
    $('body').on('click', '.radioBtn a', function () {
      const sel = $(this).data('title');
      const tog = $(this).data('toggle');
      $(this).parent().next('.' + tog).prop('value', sel);
      $(this).parent().find('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
      $(this).parent().find('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
    });
  });
  // Change Save button color on page info change
  $('#productInfo').change(function () {
    $('#btnsubmit').removeClass('btn-success').addClass('btn-warning');
  });
  $('#productInfo .radioBtn a').on('click', (function (e) {
    e.preventDefault();
    $('#btnsubmit').removeClass('btn-success').addClass('btn-warning');
  }));
</script>
<script>
  const productId = <?php echo $productId; ?>;
  function saveMainImage() {
    $('#mainImageSelect').off('submit').on('submit', (function (e) {
      e.preventDefault();
      const formData = new FormData($('#mainImageSelect')[0]);
      const productsImage = $('input#fileField').val();
      const productsImageManual = $('input[name="products_image_manual"]').val();
      if (productsImage.length > 0 || productsImageManual.length > 0) {
        $.ajax({
          type: 'POST',
          url: 'ajax.php?act=ajaxAdminProduct&method=setImage',
          processData: false,
          contentType: false,
          async: false,
          cache: false,
          data: formData,
          success: function (rawResultArray) {
            const resultArray = $.parseJSON(rawResultArray);
            let mainImageHtml;
            $('#productId').val(resultArray);
            $('#mainImageEditModal').modal('hide');
            mainImageHtml = '\n<img src="<?php echo DIR_WS_CATALOG_IMAGES; ?>' + resultArray.products_image_name + '" border="0" alt="" width="<?php echo SMALL_IMAGE_WIDTH; ?>" height="<?php echo SMALL_IMAGE_HEIGHT; ?>" class="img-thumbnail" id="mainImage">\n';
            $('#mainImageThumb').html(mainImageHtml);
            $('#mainProductImage').val(resultArray.products_image_name);
            $('#mainImagePath').html(resultArray.products_image_name);
            $('#mainImageLarger').html('<img src="<?php echo DIR_WS_CATALOG_IMAGES; ?>' + resultArray.products_image_name + '" border="0" alt="" width="<?php echo $width; ?>" height="<?php echo $height; ?>">');
            $('#button-add-main-image i').removeClass('fa-plus-circle').addClass('fa-pencil');
            $('#button-delete-main-image').show();
            $('#button-zoom-main-image').show();
            getAdditionalImages(resultArray.products_image_name);
            $('#additionalImages').show();
          }
        });
      }
    }));
  }
  function getAdditionalImages(productImage) {
    $('#additionalImagesUploaderImages').html('');
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminProduct&method=getAdditionalImages',
      data: {
        'productId': productId,
        'productImage': productImage
      }
    }).done(function (resultArray) {
      let addtlImagesHTML;
      $(resultArray.images).each(function (index, value) {
        addtlImagesHTML = '\n<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 text-center">\n';
        addtlImagesHTML += '  <div class="panel panel-info">\n';
        addtlImagesHTML += '    <div class="panel-body">\n';
        addtlImagesHTML += '      <div class="col-sm-8">\n';
        addtlImagesHTML += '        <div id="additionalImageThumb-' + value.suffix_number + '">\n';
        addtlImagesHTML += '          <img src="' + value.filename + '" width="<?php echo SMALL_IMAGE_WIDTH; ?>" height="<?php echo SMALL_IMAGE_HEIGHT; ?>" class="img-thumbnail" id="additionalImage-' + value.suffix_number + '">\n';
        addtlImagesHTML += '        </div>\n';
        addtlImagesHTML += '        <div id="additionalImagePath-' + value.suffix_number + '">' + value.filename + '</div>\n';
        addtlImagesHTML += '      </div>\n';
        addtlImagesHTML += '      <div class="col-sm-4">\n';
        addtlImagesHTML += '        <div class="btn-group-vertical" role="group">\n';
        addtlImagesHTML += '          <button type="button" id="button-delete-additional-image-' + value.suffix_number + '" class="btn btn-danger" data-original-title="<?php echo TEXT_DELETE_IMAGE ?>" data-toggle="modal" data-target="#additionalImageDeleteModal" onclick="deleteAttionalImage(' + value.filepath + ')"><i class="fa fa-trash-o" aria-hidden="true"></i></button>\n';
        addtlImagesHTML += '          <button type="button" id="button-zoom-additional-image-' + value.suffix_number + '" class="btn btn-info" data-original-title="<?php echo TEXT_CLICK_TO_ENLARGE ?>" data-toggle="modal" data-target="#additionalImageZoomModal" onclick="zoomAttionalImage(' + value.filepath + ')"><i class="fa fa-search-plus" aria-hidden="true"></i></button>\n';
        addtlImagesHTML += '        </div>\n';
        addtlImagesHTML += '      </div>\n';
        addtlImagesHTML += '    </div>\n';
        addtlImagesHTML += '  </div>\n';
        addtlImagesHTML += '</div>\n';
        $('#additionalImagesUploaderImages').html(addtlImagesHTML);
      });
      $('#new_filename').val(resultArray.new_filename);
      $('#destination').val(resultArray.destination);
      $('#additionalImagesUploaderImagesBox').show();
    });

  }
  function deleteMainImage() {
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminProduct&method=deleteMainImage',
      data: {
        'productId': productId
      }
    }).done(function () {
      $('#mainImageDeleteModal').modal('hide');
      getMessageStack();
      $('#mainImageThumb').html('<?php echo NONE; ?>');
      $('#mainImagePath').html('<?php echo NONE; ?>');
      $('#button-add-main-image i').removeClass('fa-pencil').addClass('fa-plus-circle');
      $('#button-delete-main-image').hide();
      $('#button-zoom-main-image').hide();
      $('#additionalImages').hide();
    });
  }
  function saveProduct() {
    $('#productInfo').off('submit').on('submit', (function (e) {
      e.preventDefault();
      const formData = $('#productInfo').serializeArray();
      zcJS.ajax({
        url: 'ajax.php?act=ajaxAdminProduct&method=saveProduct',
        data: formData
      }).done(function () {
        getMessageStack();
        $('button[name="insertButton"]').prop('value', '<?php echo IMAGE_SAVE; ?>').attr('name', 'saveButton').removeClass('btn-warning').addClass('btn-success');
      });
    }));
  }
  function getMessageStack() {
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminMessageStack&method=messageStack'
    }).done(function (resultArray) {
      if (resultArray) {
        $('#MessageStackText').html(resultArray.modalMessageStack);
        $('#MessageStackModal').modal('show');
        setTimeout(function () {
          $('#MessageStackModal').modal('hide');
        }, 4000);
      }
    });
  }
</script>
