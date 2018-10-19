<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script>
<<<<<<< HEAD
=======
  var collectInfoApiUrl = 'zen4all_collectInfoApi.php';
>>>>>>> develop

  function saveMainImage() {
      $("#mainImageSelect").off('submit').on('submit', (function (e) {
          e.preventDefault();
          var formData = $('#mainImageSelect').serializeArray();
          var productsImage = $('input#fileField').val();
          var productsImageManual = $('input[name="products_image_manual"]').val();
          if (productsImage.length > 0 || productsImageManual.length > 0) {
<<<<<<< HEAD
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
=======
              $.ajax({
                  url: collectInfoApiUrl,
                  method: 'POST',
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend: function () {
                      $("#err").fadeOut();
                  },
                  success: function (result) {
                      var resultArray = JSON.parse(result);
                      console.log(resultArray);
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
                  },
                  error: function (xhr, desc, err) {
                      console.log(xhr);
                      console.log("Details: " + desc + "\nError:" + err);
                  }
>>>>>>> develop
              });
          }
      }));
  }
  function getAdditionalImages(){
  
  }
  function saveProduct() {
      $("#productInfo").off('submit').on('submit', (function (e) {
          e.preventDefault();
          var formData;
          formData = $('#productInfo').serializeArray();
          zcJS.ajax({
              url: 'ajax.php?act=ajaxAdminCollectInfo&method=saveProduct',
              data: formData
          }).done(function (resultArray) {
              console.log(resultArray);
              getMessageStack();
          })
      }));
  }
  function getMessageStack() {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminCollectInfo&method=messageStack'
      }).done(function (resultArray) {
          //console.log(resultArray);
          $('#collectInfoMessageStackText').html(resultArray.modalMessageStack);
          $('#collectInfoMessageStack').modal('show');
          setTimeout(function () {
              $('#collectInfoMessageStack').modal('hide');
          }, 4000);
      });
  }
</script>
