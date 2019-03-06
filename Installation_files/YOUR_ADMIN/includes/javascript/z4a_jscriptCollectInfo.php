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
          var formData = $('#mainImageSelect').serializeArray();
          var productsImage = $('input#fileField').val();
          var productsImageManual = $('input[name="products_image_manual"]').val();
          if (productsImage.length > 0 || productsImageManual.length > 0) {
              zcJS.ajax({
                  url: 'ajax.php?act=ajaxAdminCollectInfo&method=setImage',
                  data: formData
              }).done(function (resultArray) {
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
                  getAdditionalImages();
                  $('#additionalImages').show();
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
              url: 'ajax.php?act=ajaxAdminCollectInfo&method=saveProduct',
              data: formData
          }).done(function () {
              getMessageStack();
              $('button[name="insertButton"]').prop('value', '<?php echo IMAGE_SAVE; ?>').attr('name', 'saveButton').removeClass('btn-warning').addClass('btn-success');
          });
      }));
  }
  function getMessageStack() {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminCollectInfo&method=messageStack'
      }).done(function (resultArray) {
          //console.log(resultArray);
          if (resultArray) {
              $('#collectInfoMessageStackText').html(resultArray.modalMessageStack);
              $('#collectInfoMessageStack').modal('show');
              setTimeout(function () {
                  $('#collectInfoMessageStack').modal('hide');
              }, 4000);
          }
      });
  }
</script>
