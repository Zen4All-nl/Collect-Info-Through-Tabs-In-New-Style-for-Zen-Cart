<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
  $('#select_all').change(function () {
    var checkboxes = $(this).closest('form').find(':checkbox');
    checkboxes.prop('checked', $(this).is(':checked'));
  });

  function setProductFlag(productId, flag) {

    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=setProductFlag',
      data: {
        'productId': productId,
        'flag': flag
      }
    }).done(function () {
      if (flag == '1') {
        $('#flag_' + productId).removeClass('btn-danger').addClass('btn-success').attr('title', '<?php echo IMAGE_ICON_STATUS_ON; ?>').attr('onclick', 'setProductFlag(\'' + productId + '\',\'0\')');
      } else {
        $('#flag_' + productId).removeClass('btn-success').addClass('btn-danger').attr('title', '<?php echo IMAGE_ICON_STATUS_OFF; ?>').attr('onclick', 'setProductFlag(\'' + productId + '\',\'1\')');
      }
    });
  }
  function deleteCategory(categoryId, cPath) {

    $('#cPath').val(cPath);
    $('#categoryId').val(categoryId);
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=deleteCategory',
      data: {
        'categoryId': categoryId
      }
    }).done(function (resultArray) {
      $('#delCatModalCatName').html('<strong>' + resultArray.categoryName + '</strong>');
      if (resultArray.categoryChilds != 0) {
        $('#childs_count_number').html(resultArray.categoryChilds);
        $('#childs_count').show();
      }
      if (resultArray.categoryProducts != 0) {
        $('#products_count_number').html(resultArray.categoryProducts);
        $('#products_count').show();
      }
    });
    $('#deleteCategoryModal').on('hidden.bs.modal', function () {
        $('#childs_count').hide();
        $('#products_count').hide();
        $('#childs_count_number').empty();
        $('#products_count_number').empty();
        $('#delCatModalCatName').empty();
});
  }
  function deleteCategoryConfirm() {

    $("#deleteCategoryForm").off('submit').on('submit', (function (e) {
      e.preventDefault();
      var formData;
      formData = $('#deleteCategoryForm').serializeArray();
      zcJS.ajax({
        url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=deleteCategoryConfirm',
        data: formData
      }).done(function (resultArray) {
        console.log(resultArray);
        $('#deleteCategoryModal').modal('hide');
        $('#childs_count').hide();
        $('#products_count').hide();
        $('#childs_count_number').empty();
        $('#products_count_number').empty();
        $('#delCatModalCatName').empty();
      });
    }));
  }

</script>