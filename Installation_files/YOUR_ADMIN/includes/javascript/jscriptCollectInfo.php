<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script>
  var collectInfoApiUrl = 'collectInfoApi.php';

  function saveMainImage() {
      $("#mainImageSelect").on('submit', (function (e) {
          e.preventDefault();
          var productsImage = $('input#fileField').val();
          var productsImageManual = $('input[name="products_image_manual"]').val();
          if (productsImage.length > 0 || productsImageManual.length > 0) {
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
                  },
                  error: function (xhr, desc, err) {
                      console.log(xhr);
                      console.log("Details: " + desc + "\nError:" + err);
                  }
              });
          }
      }));
  }
  function saveProduct() {
      $("#productInfo").on('submit', (function (e) {
          e.preventDefault();
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
                  getMessageStack();
              },
              error: function (xhr, desc, err) {
                  console.log(xhr);
                  console.log("Details: " + desc + "\nError:" + err);
              }
          });
      }));
  }
  function getMessageStack() {
      $.ajax({
          url: collectInfoApiUrl,
          method: 'POST',
          data: {
              'view': 'messageStack'
          },
          cache: false,
          beforeSend: function () {
              $("#err").fadeOut();
          },
          success: function (result) {
              var resultArray = JSON.parse(result);
              //console.log(resultArray);
              $('#collectInfoMessageStackText').html(resultArray.modalMessageStack);
              $('#collectInfoMessageStack').modal('show');
              setTimeout(function () {
                  $('#collectInfoMessageStack').modal('hide');
              }, 4000);
          },
          error: function (xhr, desc, err) {
              console.log(xhr);
              console.log("Details: " + desc + "\nError:" + err);
          }
      });
  }
</script>
