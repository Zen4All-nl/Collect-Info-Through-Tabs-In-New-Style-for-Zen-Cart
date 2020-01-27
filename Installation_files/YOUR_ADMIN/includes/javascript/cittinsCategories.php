<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
  // this is for activting the correct tab when comming from another page
  const getUrlParameter = function getUrlParameter(sParam) {
    const sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split('=');

      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined ? true : sParameterName[1];
      }
    }
  };
  const hash = '#' + getUrlParameter('activeTab');
  if (hash) {
    $('.nav-tabs a[href="' + hash + '"]').tab('show');
  }

</script>
<script>
  const categoryId = <?php echo (int)$cInfo->categories_id; ?>;

  function addType(add_type_all) {

    const restrictType = $('#restrict_type').val();
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminCategories&method=add_type',
      data: {
        'restrictType': restrictType,
        'categoryId': categoryId,
        'add_type_all': add_type_all
      }
    }).done(function (resultArray) {
      let newRestrictions = '';
      // add new restriction
      for (var i = 0, len = resultArray.restrictTypes.length; i < len; i++) {
        newRestrictions += '<button type="button" class="btn btn-warning" onclick="removeType(\'' + resultArray.restrictTypes[i].type_id + '\')"><?php echo IMAGE_DELETE; ?></button>&nbsp;' + resultArray.restrictTypes[i].type_name + '<br><br>';
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
      let newRestrictions = '';
      for (var i = 0, len = resultArray.restrictTypes.length; i < len; i++) {
        newRestrictions += '<button type="button" class="btn btn-warning" onclick="removeType(\'' + resultArray.restrictTypes[i].type_id + '\')"><?php echo IMAGE_DELETE; ?></button>&nbsp;' + resultArray.restrictTypes[i].type_name + '<br><br>';
      }
      $('#restrict_types').html(newRestrictions);
    });
  }
  function saveCategory() {

    $("#categoryInfo").off('submit').on('submit', (function (e) {
      e.preventDefault();
      const formData = $('#categoryInfo').serializeArray();
      zcJS.ajax({
        url: 'ajax.php?act=ajaxAdminCategories&method=save_category',
        data: formData
      }).done(function (resultArray) {
        // update hidden field action
        if (resultArray.categoryId !== '' && $('#action').val() === 'insert_category') {
          $('#action').val('update_category');
        }
        getMessageStack();
      });
    }));
  }
  function getMessageStack() {
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminMessageStack&metod=messageStack'
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
