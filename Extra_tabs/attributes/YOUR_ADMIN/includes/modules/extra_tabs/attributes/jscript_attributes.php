<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script>
  var productId = '<?php echo $productsId; ?>';

  function updateAttributeValueDropDown(optionId) {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminAttribute&method=updateValueDropDown',
          data: {
              'options_id': optionId
          }
      }).done(function (resultArray) {
          //console.log(resultArray);
          $('#OptionValue').html(resultArray.valuesDropDownArray);
      });
  }

  function copyAttributesToProduct() {
      $("#productCopyToProduct").off('submit').on('submit', (function (e) {
          e.preventDefault();
          zcJs.ajax({
              url: 'ajax.php?act=ajaxAdminAttribute&method=productCopyToProduct',
              data: new FormData(this)
          }).done(function (resultArray) {
              console.log(resultArray);
              $('#updateAttributesCopyToProduct').modal('hide');
              getMessageStack();
          });
      }));
  }

  function addAttribute() {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminAttribute&method=addAttribute',
          data: {
              'products_id': productId
          }
      }).done(function (resultArray) {
          //console.log(resultArray);
          $('input[name="products_id"]').val(resultArray.products_id);
      });
  }

  function insertAttribute() {
      $("#attributeAddForm").off('submit').on('submit', (function (e) {
          e.preventDefault();
          zcJS.ajax({
              url: 'ajax.php?act=ajaxAdminAttribute&method=insertAttribute',
              data: new FormData(this)
              }).done(function (resultArray) {
                  //console.log(resultArray);
                  $('#addAttributeModal').modal('hide');
                  // add new rows
                  var optionRowExists = document.getElementById(resultArray.optionRowId);
                  if (optionRowExists === null) {
                      if (resultArray.insertSortOrderId == '') {
                          $('#addAttribute').before(resultArray.newOption);
                          $.each(resultArray.newOptionValues, function (index, value) {
                              $('#addAttribute').before(value);
                          });
                      } else {
                          $('#' + resultArray.insertSortOrderId).before(resultArray.newOption);
                          $.each(resultArray.newOptionValues, function (index, value) {
                              $('#' + resultArray.insertSortOrderId).before(value);
                          });
                      }
                  } else {
                      $('#addAttribute').before(resultArray.newOptionValues);
                  }
          });
      }));
  }

  function editAttribute(attributeId) {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminAttribute&method=editAttribute',
          data: {
              'attribute_id': attributeId,
              'products_id': productId
          }
      }).done(function (resultArray) {
          //console.log(resultArray);
          $('#optionValuesPullDown').html(resultArray.optionValuesPullDown);
          $('#attributeFlags').html(resultArray.attributeFlags);
<?php if (ATTRIBUTES_ENABLED_IMAGES == 'true') { ?>
            $('#attributeImage').html(resultArray.attributeImage);
<?php } ?>
          $('input[name=price_prefix]').val(resultArray.attributeValuesArray.price_prefix);
          $('input[name=value_price]').val(resultArray.attributeValuesArray.options_values_price);
          $('input[name=products_attributes_weight_prefix]').val(resultArray.attributeValuesArray.products_attributes_weight_prefix);
          $('input[name=products_attributes_weight]').val(resultArray.attributeValuesArray.products_attributes_weight);
          $('input[name=products_options_sort_order]').val(resultArray.attributeValuesArray.products_options_sort_order);
          //$('input[name=attributes_image]').val(resultArray.attributeValuesArray.attributes_image);
          $('input[name=attributes_previous_image]').val(resultArray.attributeValuesArray.attributes_image);
          $('input[name=attributes_price_onetime]').val(resultArray.attributeValuesArray.attributes_price_onetime);
          $('input[name=attributes_price_factor]').val(resultArray.attributeValuesArray.attributes_price_factor);
          $('input[name=attributes_price_factor_offset]').val(resultArray.attributeValuesArray.attributes_price_factor_offset);
          $('input[name=attributes_price_factor_onetime]').val(resultArray.attributeValuesArray.attributes_price_factor_onetime);
          $('input[name=attributes_price_factor_onetime_offset]').val(resultArray.attributeValuesArray.attributes_price_factor_onetime_offset);
          $('input[name=attributes_price_factor_offset]').val(resultArray.attributeValuesArray.attributes_price_factor_offset);
          $('input[name=attributes_qty_prices]').val(resultArray.attributeValuesArray.attributes_qty_prices);
          $('input[name=attributes_qty_prices_onetime]').val(resultArray.attributeValuesArray.attributes_qty_prices_onetime);
          $('input[name=attributes_price_words]').val(resultArray.attributeValuesArray.attributes_price_words);
          $('input[name=attributes_price_words_free]').val(resultArray.attributeValuesArray.attributes_price_words_free);
          $('input[name=attributes_price_letters]').val(resultArray.attributeValuesArray.attributes_price_letters);
          $('input[name=attributes_price_letters_free]').val(resultArray.attributeValuesArray.attributes_price_letters_free);

<?php if (DOWNLOAD_ENABLED == 'true') { ?>
            $('input[name=products_attributes_maxdays]').val(resultArray.attributeValuesArray.products_attributes_maxdays);
            $('input[name=products_attributes_maxcount]').val(resultArray.attributeValuesArray.products_attributes_maxcount);
            $('select[name=products_attributes_filename]').val(resultArray.attributeValuesArray.products_attributes_filename);
            //           $('#attributeFileName option').prop('selected', false);
            //           $('#attributeFileName option[value="' + resultArray.attributeValuesArray.products_attributes_filename + '"]').prop('selected', true);
<?php } ?>

          $('input[name=attributes_id]').val(resultArray.attributeValuesArray.attributes_id);
          $('input[name=options_id]').val(resultArray.attributeValuesArray.options_id);
          $('input[name=products_id]').val(resultArray.attributeValuesArray.products_id);
      });
  }

  function saveAttribute() {
      $("#attributeEditForm").off('submit').on('submit', (function (e) {
          e.preventDefault();
          zcJS.ajax({
              url: 'ajax.php?act=ajaxAdminAttribute&method=saveAttribute',
              data: new FormData(this)
              }).done(function (resultArray) {
                  //console.log(resultArray);
                  $('#option-value-row-' + resultArray.attribute_id + '-a').html(resultArray.optionValuesRow.a);
                  $('#option-value-row-' + resultArray.attribute_id + '-b').html(resultArray.optionValuesRow.b);
                  $('#option-value-row-' + resultArray.attribute_id + '-c').html(resultArray.optionValuesRow.c);
                  $('#option-value-row-' + resultArray.attribute_id + '-d').html(resultArray.optionValuesRow.d);
                  $('#option-value-row-' + resultArray.attribute_id + '-e').html(resultArray.optionValuesRow.e);
                  $('#option-value-row-' + resultArray.attribute_id + '-f').html(resultArray.optionValuesRow.f);
                  $('#option-value-row-' + resultArray.attribute_id + '-g').html(resultArray.optionValuesRow.g);
                  $('#option-value-row-' + resultArray.attribute_id + '-h .attributes_display_only').html(resultArray.optionValuesRow.h.attributes_display_only);
                  $('#option-value-row-' + resultArray.attribute_id + '-h .product_attribute_is_free').html(resultArray.optionValuesRow.h.product_attribute_is_free);
                  $('#option-value-row-' + resultArray.attribute_id + '-h .attributes_default').html(resultArray.optionValuesRow.h.attributes_default);
                  $('#option-value-row-' + resultArray.attribute_id + '-h .attributes_discounted').html(resultArray.optionValuesRow.h.attributes_discounted);
                  $('#option-value-row-' + resultArray.attribute_id + '-h .attributes_price_base_included').html(resultArray.optionValuesRow.h.attributes_price_base_included);
                  $('#option-value-row-' + resultArray.attribute_id + '-h .attributes_required').html(resultArray.optionValuesRow.h.attributes_required);
                  $('#option-value-row-' + resultArray.attribute_id + '-i').html(resultArray.optionValuesRow.i);
                  $('#editAttributeValueModal').modal('hide');
                  getMessagestack();
          });
      }));
  }

  function deleteOptionConfirm(optionId) {
      $.ajax({
          url: 'ajax.php?act=ajaxAdminAttribute&method=deleteOptionConfirm',
          data: {
              'products_id': productId,
              'options_id': optionId
          }
          }).done(function (resultArray) {
              //console.log(resultArray);
              $('#deleteOptionText').html(resultArray.modalContent);
          });
  }

  function deleteOption() {
      $("#deleteOptionConfirm").off('submit').on('submit', (function (e) {
          e.preventDefault();
          zcJS.ajax({
              url: 'ajax.php?act=ajaxAdminAttribute&method=deleteOption',
              data: new FormData(this)
              }).done(function (result) {
                  var resultArray = JSON.parse(result);
                  //console.log(resultArray);
                  $('#deleteOptionModal').modal('hide');
                  $('#option-row-' + resultArray.optionId).remove();
                  $('tr[class*="option-id-' + resultArray.optionId + '"]').remove();
                  getMessageStack();
      });
  }));
  }

  function deleteOptionValueConfirm(attributeId) {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminAttribute&method=deleteOptionValueConfirm',
          data: {
              'products_id': productId,
              'attributes_id': attributeId
          }
        }).done(function (resultArray) {
              //console.log(resultArray);
              $('#deleteOptionValueText').html(resultArray.modalContent);
          });
  }

  function deleteOptionValue() {
      $("#deleteOptionValueConfirm").off('submit').on('submit', (function (e) {
          e.preventDefault();
          zcJS.ajax({
              url: 'ajax.php?act=ajaxAdminAttribute&method=deleteOptionValue',
              data: new FormData(this)
              }).done(function (resultArray) {
                  //console.log(resultArray);
                  $('#deleteOptionValueModal').modal('hide');
                  $('#option-value-row-' + resultArray.attributesId).remove();
                  getMessageStack();
              });
      }));
  }

  function switchFlag(flag, attributeId, flagName) {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminAttribute&method=switchFlag',
          data: {
              'attributes_id': attributeId,
              'flag': flag,
              'flag_name': flagName,
              'products_id': productId
          }
        }).done(function (resultArray) {
              //console.log(resultArray);
              $('#flag-' + attributeId + '-' + flagName).attr('onclick','switchFlag(\'' + resultArray.flagValue + '\', \'' + attributeId + '\', \'' + flagName + '\')');
              if (resultArray.flagValue == 1) {
                $('#flag-' + attributeId + '-' + flagName).addClass('flagNotActive');
                $('#flag-' + attributeId + '-' + flagName + ' i').removeClass('fa-check').addClass('fa-times');
              } else {
                $('#flag-' + attributeId + '-' + flagName).removeClass('flagNotActive');
                $('#flag-' + attributeId + '-' + flagName + ' i').removeClass('fa-times').addClass('fa-check');
              }
          });
  }

  function getMessageStack() {
      zcJS.ajax({
          url: 'ajax.php?act=ajaxAdminAttribute&method=messageStack'
        }).done(function (resultArray) {
              //console.log(resultArray);
              $('#attributesMessageStackText').html(resultArray.modalMessageStack);
              $('#attributesMessageStack').modal('show');
              setTimeout(function () {
                  $('#attributesMessageStack').modal('hide');
              }, 4000);
          });
  }
</script>