<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script>
  var categoryId = <?php echo (int)$cInfo->categories_id; ?>;

  function addType(add_type_all) {

      var restrictType = $('#restrict_type').val();
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminCategories&method=add_type',
          data: {
              'restrictType': restrictType,
              'categoryId': categoryId,
              'add_type_all': add_type_all
          }
      }).done(function (resultArray) {
          var newRestrictions = '';
          // add new restriction
          for (var i = 0, len = resultArray.restrictTypes.length; i < len; i++) {
              newRestrictions += '<button type="button" class="btn btn-warning" onclick="removeType(\'' + resultArray['restrictTypes'][i]['type_id'] + '\')"><?php echo IMAGE_DELETE; ?></button>&nbsp;' + resultArray['restrictTypes'][i]['type_name'] + '<br><br>';
          }
          $('#restrict_types').html(newRestrictions);
          //  $('#mainImageEditModal').modal('hide');
      });
  }
  function removeType(restrictType) {

      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminCategories&method=remove_type',
          data: {
              'restrictType': restrictType,
              'categoryId': categoryId
          }
      }).done(function (resultArray) {
          var newRestrictions = '';
          for (var i = 0, len = resultArray['restrictTypes'].length; i < len; i++) {
              console.log(i);
              newRestrictions += '<button type="button" class="btn btn-warning" onclick="removeType(\'' + resultArray['restrictTypes'][i]['type_id'] + '\')"><?php echo IMAGE_DELETE; ?></button>&nbsp;' + resultArray['restrictTypes'][i]['type_name'] + '<br><br>';
          }
          $('#restrict_types').html(newRestrictions);
      });
  }
  function saveCategory() {

      $("#categoryInfo").off('submit').on('submit', (function (e) {
          e.preventDefault();
          zcJS.ajax({
              url: 'ajax.php?act=ajaxAdminCategories&method=save_category',
              data: new FormData(this)
          }).done(function (resultArray) {
              console.log(resultArray);
              // update hidden field action
              if (resultArray['categoryId'] !== '' && $('#action').val() === 'insert_category') {
                  $('#action').val('update_category');
              }
              getMessageStack();
          });
      }));
  }
  function getMessageStack() {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminCategories&metod=messageStack'
      }).done(function (resultArray) {
          //console.log(resultArray);
          $('#categoryMessageStackText').html(resultArray.modalMessageStack);
          $('#categoryMessageStackText').modal('show');
          setTimeout(function () {
              $('#categoryMessageStackText').modal('hide');
          }, 4000);
      });
  }
</script>
