<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script>
  var categoriesApiUrl = 'z4a_categoriesApi.php';
  var categoryId = <?php echo (int)$cInfo->categories_id; ?>;

  function addType(add_type_all) {

      var restrictType = $('#restrict_type').val();
      var view = 'add_type';
      $.ajax({
          url: categoriesApiUrl,
          method: 'POST',
          data: {
              'view': view,
              'restrictType': restrictType,
              'categoryId': categoryId,
              'add_type_all': add_type_all
          },
          cache: false,
          beforeSend: function () {
              $("#err").fadeOut();
          },
          success: function (result) {
              var resultArray = JSON.parse(result);
              var newRestrictions = '';
              // add new restriction
              for (var i = 0, len = resultArray.restrictTypes.length; i < len; i++) {
                  newRestrictions += '<button type="button" class="btn btn-warning" onclick="removeType(\'' + resultArray['restrictTypes'][i]['type_id'] + '\')"><?php echo IMAGE_DELETE; ?></button>&nbsp;' + resultArray['restrictTypes'][i]['type_name'] + '<br><br>';
              }
              $('#restrict_types').html(newRestrictions);
              //  $('#mainImageEditModal').modal('hide');
          },
          error: function (xhr, desc, err) {
              console.log(xhr);
              console.log("Details: " + desc + "\nError:" + err);
          }
      });
  }
  function removeType(restrictType) {

      var view = 'remove_type';
      $.ajax({
          url: categoriesApiUrl,
          method: 'POST',
          data: {
              'view': view,
              'restrictType': restrictType,
              'categoryId': categoryId
          },
          cache: false,
          beforeSend: function () {
              $("#err").fadeOut();
          },
          success: function (result) {
              var resultArray = JSON.parse(result);
              var newRestrictions = '';
              for (var i = 0, len = resultArray['restrictTypes'].length; i < len; i++) {
                  console.log(i);
                  newRestrictions += '<button type="button" class="btn btn-warning" onclick="removeType(\'' + resultArray['restrictTypes'][i]['type_id'] + '\')"><?php echo IMAGE_DELETE; ?></button>&nbsp;' + resultArray['restrictTypes'][i]['type_name'] + '<br><br>';
              }
              $('#restrict_types').html(newRestrictions);
          },
          error: function (xhr, desc, err) {
              console.log(xhr);
              console.log("Details: " + desc + "\nError:" + err);
          }
      });
  }
  function saveCategory() {

        $("#categoryInfo").off('submit').on('submit', (function (e) {
          e.preventDefault();
          $.ajax({
              url: categoriesApiUrl,
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
                  // update hidden field action
                  if (resultArray['categoryId'] !== '' && $('#action').val() === 'insert_category') {
                    $('#action').val('update_category');
                  }
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
          url: categoriesApiUrl,
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
              $('#categoryMessageStackText').html(resultArray.modalMessageStack);
              $('#categoryMessageStackText').modal('show');
              setTimeout(function () {
                  $('#categoryMessageStackText').modal('hide');
              }, 4000);
          },
          error: function (xhr, desc, err) {
              console.log(xhr);
              console.log("Details: " + desc + "\nError:" + err);
          }
      });
  }
</script>
