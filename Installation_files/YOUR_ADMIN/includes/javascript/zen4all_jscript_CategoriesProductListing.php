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
        $('#pFlag_' + productId).removeClass('btn-danger').addClass('btn-success').attr('title', '<?php echo IMAGE_ICON_STATUS_ON; ?>').attr('onclick', 'setProductFlag(\'' + productId + '\', \'0\')');
      } else {
        $('#pFlag_' + productId).removeClass('btn-success').addClass('btn-danger').attr('title', '<?php echo IMAGE_ICON_STATUS_OFF; ?>').attr('onclick', 'setProductFlag(\'' + productId + '\', \'1\')');
      }
    });
  }
  function setCategoryFlag(categoryId, cPath, flag) {
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=setCategoryFlag',
      data: {
        'categoryId': categoryId,
        'flag': flag,
        'cPath': cPath,
        'current_category_id': <?php echo $current_category_id; ?>
      }
    }).done(function (resultArray) {
      $('#setFlagCatIdHeading').html(resultArray.path + ' > ' + resultArray.categoryName);
      $('#hiddenCategoriesStatus').val(flag);
      $('#hiddenCategoriesId').val(categoryId);
      $('#hiddenCPath').val(cPath);
      if (flag == 1 && resultArray.hasCategorySubcategories == true) {
        $('#FlagRadioHasCategorySubcategories').html(
                '\n<div class="col-sm-3"><?php echo TEXT_SUBCATEGORIES_STATUS_INFO; ?></div>\n' +
                '<div class="col-sm-9">\n' +
                '  <div class="radio">\n' +
                '    <label><input type="radio" name="set_subcategories_status" value="set_subcategories_status_off" checked><?php echo TEXT_SUBCATEGORIES_STATUS_OFF; ?></label>\n' +
                '  </div>\n' +
                '  <div class="radio">\n' +
                '    <label><input type="radio" name="set_subcategories_status" value="set_subcategories_status_nochange"><?php echo TEXT_SUBCATEGORIES_STATUS_NOCHANGE; ?></label>\n' +
                '  </div>\n' +
                '</div>\n'
                );
      }
      if (flag == 1 && resultArray.getProductsToCategories > 0) {
        $('#FlagRadioGetProductsToCategories').html(
                '\n<div class="col-sm-3"><?php echo TEXT_PRODUCTS_STATUS_INFO; ?></div>\n' +
                '<div class="col-sm-9">\n' +
                '  <div class="radio">\n' +
                '    <label><input type="radio" name="set_products_status" value="set_products_status_off" checked><?php echo TEXT_SUBCATEGORIES_STATUS_OFF; ?></label>\n' +
                '  </div>\n' +
                '  <div class="radio">' +
                '    <label><input type="radio" name="set_products_status" value="set_products_status_nochange"><?php echo TEXT_PRODUCTS_STATUS_NOCHANGE; ?></label>\n' +
                '  </div>\n' +
                '</div>\n'
                );
      }
      if (flag == 0 && resultArray.hasCategorySubcategories == true) {
        $('#FlagRadioHasCategorySubcategories').html(
                '\n<div class="col-sm-3"><?php echo TEXT_SUBCATEGORIES_STATUS_INFO; ?></div>\n' +
                '<div class="col-sm-9">\n' +
                '  <div class="radio">\n' +
                '    <label><input type="radio" name="set_subcategories_status" value="set_subcategories_status_on" checked><?php echo TEXT_SUBCATEGORIES_STATUS_ON; ?></label>\n' +
                '  </div>\n' +
                '  <div class="radio">\n' +
                '    <label><input type="radio" name="set_subcategories_status" value="set_subcategories_status_nochange"><?php echo TEXT_SUBCATEGORIES_STATUS_NOCHANGE; ?></label>\n' +
                '  </div>\n' +
                '</div>\n'
                );
      }
      if (flag == 0 && resultArray.getProductsToCategories > 0) {
        $('#FlagRadioGetProductsToCategories').html(
                '\n<div class="col-sm-3"><?php echo TEXT_PRODUCTS_STATUS_INFO; ?></div>\n' +
                '<div class="col-sm-9">\n' +
                '  <div class="radio">\n' +
                '    <label><input type="radio" name="set_products_status" value="set_products_status_on" checked><?php echo TEXT_PRODUCTS_STATUS_ON; ?></label>\n' +
                '  </div>\n' +
                '  <div class="radio">\n' +
                '    <label><input type="radio" name="set_products_status" value="set_products_status_nochange"><?php echo TEXT_PRODUCTS_STATUS_NOCHANGE; ?></label>\n' +
                '  </div>\n' +
                '</div>\n'
                );
      }
    });
    $('#setCategoryFlagModal').on('hidden.bs.modal', function () {
      $('#FlagRadioHasCategorySubcategories').empty();
      $('#FlagRadioGetProductsToCategories').empty();
    });
  }
  function setCategoryFlagConfirm() {
    $("#setCategoryFlagForm").off('submit').on('submit', (function (e) {
      e.preventDefault();
      var formData;
      formData = $('#setCategoryFlagForm').serializeArray();
      zcJS.ajax({
        url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=setCategoryFlagConfirm',
        data: formData
      }).done(function (resultArray) {
        $('#setCategoryFlagModal').modal('hide');
        $('#FlagRadioHasCategorySubcategories').empty();
        $('#FlagRadioGetProductsToCategories').empty();
        if (resultArray.newFlag == '1') {
          $('#cFlag_' + resultArray.categoryId).removeClass('btn-danger').addClass('btn-success').attr('title', '<?php echo IMAGE_ICON_STATUS_ON; ?>').attr('onclick', 'setCategoryFlag(\'' + resultArray.categoryId + '\', \'' + resultArray.cPath + '\', \'1\')');
        } else {
          $('#cFlag_' + resultArray.categoryId).removeClass('btn-success').addClass('btn-danger').attr('title', '<?php echo IMAGE_ICON_STATUS_OFF; ?>').attr('onclick', 'setCategoryFlag(\'' + resultArray.categoryId + '\', \'' + resultArray.cPath + '\', \'0\')');
        }
        $('tr#cID_' + resultArray.categoryId + ' td.ColumnQuantity').html(resultArray.totalProductsOn + '<?php echo TEXT_PRODUCTS_STATUS_ON_OF; ?>' + resultArray.totalProducts + '<?php echo TEXT_PRODUCTS_STATUS_ACTIVE; ?>');
      });
    }));
  }
  function deleteCategory(categoryId, cPath) {

    $('#cPath').val(cPath);
    $('#deleteCategoryId').val(categoryId);
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
        $('#cID_' + resultArray.cID).empty();
      });
    }));
  }
  function moveCategory(categoryId, cPath) {
    $('#cPath').val(cPath);
    $('#moveCategoryId').val(categoryId);
    $("#moveCategoryForm select").val(categoryId);
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=moveCategory',
      data: {
        'categoryId': categoryId
      }
    }).done(function (resultArray) {
      $('.category_name').html(resultArray.categoryName)
    });
    $('#moveCategoryModal').on('hidden.bs.modal', function () {
      $('.category_name').empty();
    });
  }
  function moveCategoryConfirm() {
    $("#moveCategoryForm").off('submit').on('submit', (function (e) {
      e.preventDefault();
      var formData;
      formData = $('#moveCategoryForm').serializeArray();
      zcJS.ajax({
        url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=moveCategoryConfirm',
        data: formData
      }).done(function (resultArray) {
        console.log(resultArray);
        $('#moveCategoryModal').modal('hide');
        $('.category_name').empty();
        $('#cID_' + resultArray.cID).empty();
        getMessageStack();
      });
    }));
  }

  function getMessageStack() {
    zcJS.ajax({
      url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=messageStack'
    }).done(function (resultArray) {
      //console.log(resultArray);
      if (resultArray) {
        $('#categoriesProductListingMessageStackText').html(resultArray.modalMessageStack);
        $('#categoriesProductListingMessageStack').modal('show');
        setTimeout(function () {
          $('#collectInfoMessageStack').modal('hide');
        }, 4000);
      }
    });
  }
  $(document).ready(function () {
    /* BOF Column hiding*/
    $(".checkbox-menu").on("change", "input[type='checkbox']", function () {
      $(this).closest("li").toggleClass("active", this.checked);
    });
    $(document).on('click', '.allow-focus', function (e) {
      e.stopPropagation();
    });

    $("#columnDropDown input:checkbox:not(:checked)").each(function () {
      var column = 'table .' + $(this).attr("name");
      $(column).hide();
    });

    $("#columnDropDown input:checkbox").click(function () {
      var column = "table ." + $(this).attr("name");
      $(column).toggle();
      zcJS.ajax({
        url: 'ajax.php?act=ajaxAdminCategoriesProductListing&method=setSessionColumnValue',
        data: {
          'column': $(this).attr("name")
        }
      }).done(function (result) {
        console.log(result);
      });
    });
    /* EOF Column hiding*/
  });
</script>