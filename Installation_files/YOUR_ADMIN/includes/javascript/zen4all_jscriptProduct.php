<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script>
  function saveMainImage() {
    $("#mainImageSelect").off('submit').on('submit', (function (e) {
      e.preventDefault();
      var formData = new FormData($('#mainImageSelect')[0]);
      var productsImage = $('input#fileField').val();
      var productsImageManual = $('input[name="products_image_manual"]').val();
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
            var resultArray = jQuery.parseJSON(rawResultArray);
            console.log(resultArray);
            $('#productId').val(resultArray);
            $('#mainImageEditModal').modal('hide');
            mainImageHtml = '<img src="<?php echo DIR_WS_CATALOG_IMAGES; ?>' + resultArray['products_image_name'] + '" border="0" alt="" width="<?php echo SMALL_IMAGE_WIDTH; ?>" height="<?php echo SMALL_IMAGE_HEIGHT; ?>" class="img-thumbnail" id="mainImage">';
            mainImageHtml += '<br/>';
            mainImageHtml += '<?php echo TEXT_CLICK_TO_ENLARGE; ?>';
            $('#mainImageThumb').html(mainImageHtml);
            $('#mainProductImage').val(resultArray['products_image_name']);
            $('#mainImagePath').html(resultArray['products_image_name']);
            $('#mainImageLarger').html('<img src="<?php echo DIR_WS_CATALOG_IMAGES; ?>' + resultArray['products_image_name'] + '" border="0" alt="" width="<?php echo $width; ?>" height="<?php echo $height; ?>">');
            $('#button-add-main-image i').removeClass('fa-plus-circle').addClass('fa-pencil');
            getAdditionalImages();
            $('#additionalImages').show();
          }
        });
      }
    }));
  }
  function getAdditionalImages() {

  }
  function saveProduct() {
    $("#productInfo").off('submit').on('submit', (function (e) {
      e.preventDefault();
      var formData;
      formData = $('#productInfo').serializeArray();
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
