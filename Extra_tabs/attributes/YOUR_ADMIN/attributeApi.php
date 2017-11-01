<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('includes/application_top.php');
include (DIR_WS_LANGUAGES . $_SESSION['language'] . '/attributes_controller.php');
$returnData = array();
$data = $_POST;
$productId = (int)$data['products_id'];
/* $returnData['dataToApi'] is used for debugging, to see which data is send to api. */
// $returnData['dataToApi'] = $data; // remove commets to see the data in the colsolelog.

switch ($data['view']) {
  case 'updateValueDropDown' : {
      $optionValuesQuery = "SELECT povpo.products_options_values_id,
                                   pov.products_options_values_name
                            FROM " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " povpo,
                                 " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
                            WHERE povpo.products_options_id = " . $data['options_id'] . "
                            AND povpo.products_options_values_id = pov.products_options_values_id
                            AND pov.language_id = " . (int)$_SESSION['languages_id'] . "
                            ORDER BY pov.products_options_values_name";

      $optionValues = $db->Execute($optionValuesQuery);

      $valuesDropDownArray = '';
      foreach ($optionValues as $optionValue) {
        $text = $optionValue['products_options_values_name'] . ($optionValue['products_options_values_id'] == 0 ? '/UPLOAD FILE' : '') . ' [ #' . $optionValue['products_options_values_id'] . ' ] ';
        $valuesDropDownArray .= '<option value="' . zen_output_string($optionValue['products_options_values_id']) . '">' . zen_output_string($text, array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
      }
      $returnData['valuesDropDownArray'] = $valuesDropDownArray;
      break;
    }
  // update by product
  case 'updateAttributeSort': {
      if (!zen_has_product_attributes($productId, 'false')) {
        $messageStack->add_session(SUCCESS_PRODUCT_UPDATE_SORT_NONE . $productId . ' ' . zen_get_products_name($productId, $_SESSION['languages_id']), 'error');
      } else {
        zen_update_attributes_products_option_values_sort_order($productId);
        $messageStack->add_session(SUCCESS_PRODUCT_UPDATE_SORT . $productId . ' ' . zen_get_products_name($productId, $_SESSION['languages_id']), 'success');
      }
      break;
    }
  case 'productCopyToProduct' : {
      $copy_attributes_delete_first = ($data['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
      $copy_attributes_duplicates_skipped = ($data['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
      $copy_attributes_duplicates_overwrite = ($data['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
      zen_copy_products_attributes($productId, (int)$data['products_update_id']);
      break;
    }
  case 'productCopyToCategory' : {
      $copy_attributes_delete_first = ($data['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
      $copy_attributes_duplicates_skipped = ($data['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
      $copy_attributes_duplicates_overwrite = ($data['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
      if ($data['categories_update_id'] == '') {
        $messageStack->add_session(WARNING_PRODUCT_COPY_TO_CATEGORY_NONE . ' ID#' . $productsId, 'warning');
      } else {
        $copy_to_category = $db->Execute("SELECT products_id
                                          FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                          WHERE categories_id = '" . (int)$data['categories_update_id'] . "'");
        foreach ($copy_to_category as $copy) {
          zen_copy_products_attributes((int)$productsId, $copy['products_id']);
        }
      }
    }
  case 'addAttribute' : {
      $returnData['products_id'] = $data['products_id'];
      break;
    }
  case 'editAttribute' : {
      $attributeId = (int)$data['attribute_id'];
      $attributeValuesQuery = "SELECT pa.*
                               FROM (" . TABLE_PRODUCTS_ATTRIBUTES . " pa
                               LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON pa.options_id = po.products_options_id
                                 AND po.language_id = " . (int)$_SESSION['languages_id'] . ")
                               WHERE products_id = " . $productId . "
                               AND products_attributes_id = " . $attributeId . "
                               ORDER BY LPAD(po.products_options_sort_order,11,'0'),
                                        LPAD(pa.products_options_sort_order,11,'0')";
      $attributeValues = $db->Execute($attributeValuesQuery);

      $attributeValuesArray = [
        'products_attributes_id' => $attributeValues->fields['products_attributes_id'],
        'products_id' => $attributeValues->fields['products_id'],
        'options_id' => $attributeValues->fields['options_id'],
        'attributes_id' => $attributeValues->fields['products_attributes_id'],
        'options_values_id' => $attributeValues->fields['options_values_id'],
        'options_values_price' => $attributeValues->fields['options_values_price'],
        'price_prefix' => $attributeValues->fields['price_prefix'],
        'products_options_sort_order' => $attributeValues->fields['products_options_sort_order'],
        'product_attribute_is_free' => $attributeValues->fields['product_attribute_is_free'],
        'products_attributes_weight' => $attributeValues->fields['products_attributes_weight'],
        'products_attributes_weight_prefix' => $attributeValues->fields['products_attributes_weight_prefix'],
        'attributes_display_only' => $attributeValues->fields['attributes_display_only'],
        'attributes_default' => $attributeValues->fields['attributes_default'],
        'attributes_discounted' => $attributeValues->fields['attributes_discounted'],
        'attributes_image' => $attributeValues->fields['attributes_image'],
        'attributes_price_base_included' => $attributeValues->fields['attributes_price_base_included'],
        'attributes_price_onetime' => $attributeValues->fields['attributes_price_onetime'],
        'attributes_price_factor' => $attributeValues->fields['attributes_price_factor'],
        'attributes_price_factor_offset' => $attributeValues->fields['attributes_price_factor_offset'],
        'attributes_price_factor_onetime' => $attributeValues->fields['attributes_price_factor_onetime'],
        'attributes_price_factor_onetime_offset' => $attributeValues->fields['attributes_price_factor_onetime_offset'],
        'attributes_qty_prices' => $attributeValues->fields['attributes_qty_prices'],
        'attributes_qty_prices_onetime' => $attributeValues->fields['attributes_qty_prices_onetime'],
        'attributes_price_words' => $attributeValues->fields['attributes_price_words'],
        'attributes_price_words_free' => $attributeValues->fields['attributes_price_words_free'],
        'attributes_price_letters' => $attributeValues->fields['attributes_price_letters'],
        'attributes_price_letters_free' => $attributeValues->fields['attributes_price_letters_free'],
        'attributes_required' => $attributeValues->fields['attributes_required']
      ];

      if (DOWNLOAD_ENABLED == 'true') {
        $attributeDownloadQuery = "SELECT products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                                       FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                                       WHERE products_attributes_id = " . (int)$attributeValues->fields['products_attributes_id'];
        $attributeDownload = $db->Execute($attributeDownloadQuery);
        if ($attributeDownload->RecordCount() > 0) {
          $attributeValuesArray['products_attributes_filename'] = $attributeDownload->fields['products_attributes_filename'];
          $attributeValuesArray['products_attributes_maxdays'] = $attributeDownload->fields['products_attributes_maxdays'];
          $attributeValuesArray['products_attributes_maxcount'] = $attributeDownload->fields['products_attributes_maxcount'];
        }
      }

// set radio values attributes_display_only
      switch ($attributeValuesArray['attributes_display_only']) {
        case '0': $on_attributes_display_only = false;
          $off_attributes_display_only = true;
          break;
        case '1': $on_attributes_display_only = true;
          $off_attributes_display_only = false;
          break;
        default: $on_attributes_display_only = false;
          $off_attributes_display_only = true;
      }
// set radio values attributes_default
      switch ($attributeValuesArray['product_attribute_is_free']) {
        case '0': $on_product_attribute_is_free = false;
          $off_product_attribute_is_free = true;
          break;
        case '1': $on_product_attribute_is_free = true;
          $off_product_attribute_is_free = false;
          break;
        default: $on_product_attribute_is_free = false;
          $off_product_attribute_is_free = true;
      }
// set radio values attributes_default
      switch ($attributeValuesArray['attributes_default']) {
        case '0': $on_attributes_default = false;
          $off_attributes_default = true;
          break;
        case '1': $on_attributes_default = true;
          $off_attributes_default = false;
          break;
        default: $on_attributes_default = false;
          $off_attributes_default = true;
      }
// set radio values attributes_discounted
      switch ($attributeValuesArray['attributes_discounted']) {
        case '0': $on_attributes_discounted = false;
          $off_attributes_discounted = true;
          break;
        case '1': $on_attributes_discounted = true;
          $off_attributes_discounted = false;
          break;
        default: $on_attributes_discounted = false;
          $off_attributes_discounted = true;
      }
// set radio values attributes_price_base_included
      switch ($attributeValuesArray['attributes_price_base_included']) {
        case '0': $on_attributes_price_base_included = false;
          $off_attributes_price_base_included = true;
          break;
        case '1': $on_attributes_price_base_included = true;
          $off_attributes_price_base_included = false;
          break;
        default: $on_attributes_price_base_included = false;
          $off_attributes_price_base_included = true;
      }
// set radio values attributes_required
      switch ($attributeValuesArray['attributes_required']) {
        case '0': $on_attributes_required = false;
          $off_attributes_required = true;
          break;
        case '1': $on_attributes_required = true;
          $off_attributes_required = false;
          break;
        default: $on_attributes_required = false;
          $off_attributes_required = true;
      }

      if (ATTRIBUTES_ENABLED_IMAGES == 'true') {
// set image overwrite
        $attr_on_overwrite = true;
        $attr_off_overwrite = false;
// set image delete
        $attr_on_image_delete = false;
        $attr_off_image_delete = true;

// attributes images
        $attr_dir_info = zen_build_subdirectories_array(DIR_FS_CATALOG_IMAGES);
        if ($attributeValuesArray['attributes_image'] != '') {
          $attr_default_directory = substr($attributeValuesArray['attributes_image'], 0, strpos($attributeValuesArray['attributes_image'], '/') + 1);
        } else {
          $attr_default_directory = 'attributes/';
        }
        $attributeImage = '';
        $attributeImage .= '<div class="row">' . "\n";
        $attributeImage .= '<div class="col-sm-2">' . "\n";
        $attributeImage .= ($attributeValuesArray['attributes_image'] != '' ? zen_image(DIR_WS_CATALOG_IMAGES . $attributeValuesArray['attributes_image'], $attributeValuesArray['attributes_image'], '', '', 'class="img-thumbnail"') : 'No image selected') . "\n";
        $attributeImage .= $attributeValuesArray['attributes_image'] . "\n";
        $attributeImage .= '</div>' . "\n";
        $attributeImage .= '<div class="col-sm-10">' . "\n";
        $attributeImage .= zen_draw_file_field('attributes_image', '', 'class="form-control"') . zen_draw_hidden_field('attributes_previous_image', $attributeValuesArray['attributes_image']) . "\n";
        $attributeImage .= '</div>' . "\n";
        $attributeImage .= '</div>' . "\n";
        $attributeImage .= '<div class="row">' . "\n";
        $attributeImage .= '<div class="col-sm-6">' . zen_draw_label(TEXT_ATTRIBUTES_IMAGE_DIR, 'img_dir', 'class="control-label"') . zen_draw_pull_down_menu('img_dir', $attr_dir_info, $attr_default_directory, 'class="form-control"') . '</div>' . "\n";
        $attributeImage .= '<div class="col-xs-6 col-sm-3">' . "\n";
        $attributeImage .= zen_draw_label(TEXT_IMAGES_OVERWRITE, 'attributes_overwrite', 'çlass="control-label"') . "\n";
        $attributeImage .= '<div class="btn-group" data-toggle="buttons">' . "\n";
        $attributeImage .= '<label class="btn ' . ($attr_off_overwrite == true ? 'active' : '') . '">' . "\n";
        $attributeImage .= zen_draw_radio_field('attributes_overwrite', '0', $attr_off_overwrite) . "\n";
        $attributeImage .= '<i class="fa fa-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<i class="fa fa-dot-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<span>' . TABLE_HEADING_NO . '</span>' . "\n";
        $attributeImage .= '</label>' . "\n";
        $attributeImage .= '<label class="btn ' . ($attr_on_overwrite == true ? 'active' : '') . '">' . "\n";
        $attributeImage .= zen_draw_radio_field('attributes_overwrite', '1', $attr_on_overwrite) . "\n";
        $attributeImage .= '<i class="fa fa-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<i class="fa fa-dot-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<span>' . TABLE_HEADING_YES . '</span>' . "\n";
        $attributeImage .= '</label>' . "\n";
        $attributeImage .= '</div>' . "\n";
        $attributeImage .= '</div>' . "\n";
        $attributeImage .= '<div class="col-xs-6 col-sm-3">' . "\n";
        $attributeImage .= zen_draw_label(TEXT_IMAGES_DELETE, 'attributes_image_delete', 'çlass="control-label"') . "\n";
        $attributeImage .= '<div class="btn-group" data-toggle="buttons">' . "\n";
        $attributeImage .= '<label class="btn ' . ($attr_off_image_delete == true ? 'active' : '') . '">' . "\n";
        $attributeImage .= zen_draw_radio_field('attributes_image_delete', '0', $attr_off_image_delete) . "\n";
        $attributeImage .= '<i class="fa fa-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<i class="fa fa-dot-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<span>' . TABLE_HEADING_NO . '</span>' . "\n";
        $attributeImage .= '</label>' . "\n";
        $attributeImage .= '<label class="btn ' . ($attr_on_image_delete == true ? 'active' : '') . '">' . "\n";
        $attributeImage .= zen_draw_radio_field('attributes_image_delete', '1', $attr_on_image_delete) . "\n";
        $attributeImage .= '<i class="fa fa-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<i class="fa fa-dot-circle-o fa-lg"></i>' . "\n";
        $attributeImage .= '<span>' . TABLE_HEADING_YES . '</span>' . "\n";
        $attributeImage .= '</label>' . "\n";
        $attributeImage .= '</div>' . "\n";
        $attributeImage .= '</div>' . "\n";
        $attributeImage .= '</div>' . "\n";

        $returnData['attributeImage'] = $attributeImage;
      }
      $optionValues = $db->Execute("SELECT pov.products_options_values_id, pov.products_options_values_name
                                    FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
                                    LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " povtpo ON pov.products_options_values_id = povtpo.products_options_values_id
                                    WHERE pov.language_id ='" . (int)$_SESSION['languages_id'] . "'
                                    AND povtpo.products_options_id='" . $attributeValuesArray['options_id'] . "'
                                    ORDER BY pov.products_options_values_name");

      $optionValuesArray = [];
      foreach ($optionValues as $value) {
        $optionValuesArray[] = [
          'id' => $value['products_options_values_id'],
          'text' => $value['products_options_values_name']
        ];
      }
      $returnData['optionValuesPullDown'] = zen_draw_pull_down_menu('values_id', $optionValuesArray, $attributeValuesArray['options_values_id'], 'size="10" class="form-control"');
      // set flag radio's

      $attributeFlags = '';
      $attributeFlags .= '<div class="col-sm-2" style="background-color: #ff0;padding-bottom: 10px;">' . "\n";
      $attributeFlags .= zen_draw_label(TEXT_ATTRIBUTES_DISPLAY_ONLY, 'attributes_display_only', 'class="control-label"') . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_display_only', '0', $off_attributes_display_only) . TABLE_HEADING_NO . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '<label class="radio-inline">';
      $attributeFlags .= zen_draw_radio_field('attributes_display_only', '1', $on_attributes_display_only) . TABLE_HEADING_YES . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '</div>' . "\n";
      $attributeFlags .= '<div class="col-sm-2" style="background-color: #2c54f5;padding-bottom: 10px;">' . "\n";
      $attributeFlags .= zen_draw_label(TEXT_ATTRIBUTES_IS_FREE, 'product_attribute_is_free', 'class="control-label"') . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('product_attribute_is_free', '0', $off_product_attribute_is_free) . TABLE_HEADING_NO . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('product_attribute_is_free', '1', $on_product_attribute_is_free) . TABLE_HEADING_YES . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '</div>' . "\n";
      $attributeFlags .= '<div class="col-sm-2" style="background-color: #ffa346;padding-bottom: 10px;">' . "\n";
      $attributeFlags .= zen_draw_label(TEXT_ATTRIBUTES_DEFAULT, 'product_attribute_is_free', 'class="control-label"') . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_default', '0', $off_attributes_default) . TABLE_HEADING_NO . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_default', '1', $on_attributes_default) . TABLE_HEADING_YES . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '</div>' . "\n";
      $attributeFlags .= '<div class="col-sm-2" style="background-color: #f0f;padding-bottom: 10px;">' . "\n";
      $attributeFlags .= zen_draw_label(TEXT_ATTRIBUTE_IS_DISCOUNTED, 'attributes_discounted', 'class="control-label"') . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_discounted', '0', $off_attributes_discounted) . TABLE_HEADING_NO . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_discounted', '1', $on_attributes_discounted) . TABLE_HEADING_YES . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '</div>' . "\n";
      $attributeFlags .= '<div class="col-sm-2" style="background-color: #d200f0;padding-bottom: 10px;">' . "\n";
      $attributeFlags .= zen_draw_label(TEXT_ATTRIBUTE_PRICE_BASE_INCLUDED, 'attributes_price_base_included', 'class="control-label"') . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_price_base_included', '0', $off_attributes_price_base_included) . TABLE_HEADING_NO . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_price_base_included', '1', $on_attributes_price_base_included) . TABLE_HEADING_YES . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '</div>' . "\n";
      $attributeFlags .= '<div class="col-sm-2" style="background-color: #FF0606;padding-bottom: 10px;">' . "\n";
      $attributeFlags .= zen_draw_label(TEXT_ATTRIBUTES_REQUIRED, 'attributes_required', 'class="control-label"') . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_required', '0', $off_attributes_required) . TABLE_HEADING_NO . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '<label class="radio-inline">' . "\n";
      $attributeFlags .= zen_draw_radio_field('attributes_required', '1', $on_attributes_required) . TABLE_HEADING_YES . "\n";
      $attributeFlags .= '</label>' . "\n";
      $attributeFlags .= '</div>' . "\n";

      $returnData['attributeFlags'] = $attributeFlags;

      $returnData['attributeValuesArray'] = $attributeValuesArray;
      break;
    }
  case 'insertAttribute' : {
      $current_image_name = '';
      $newOptionvalues[] = array();
      for ($i = 0; $i < sizeof($data['values_id']); $i++) {
// check for duplicate and block them
        $check_duplicate = $db->Execute("SELECT *
                                         FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                                         WHERE products_id = " . $productId . "
                                         AND options_id = " . (int)$data['options_id'] . "
                                         AND options_values_id = " . (int)$data['values_id'][$i]);
        if ($check_duplicate->RecordCount() > 0) {
          // do not add duplicates -- give a warning
          $messageStack->add_session(ATTRIBUTE_WARNING_DUPLICATE . ' - ' . zen_options_name((int)$data['options_id']) . ' : ' . zen_values_name((int)$data['values_id'][$i]), 'error');
        } else {
// For TEXT and FILE option types, ignore option value entered by administrator and use PRODUCTS_OPTIONS_VALUES_TEXT instead.
          $optionInformation = $db->Execute("SELECT products_options_type, products_options_sort_order
                                             FROM " . TABLE_PRODUCTS_OPTIONS . "
                                             WHERE products_options_id = " . (int)$data['options_id']);
          $values_id = zen_db_prepare_input((($optionInformation->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_TEXT) || ( $optionInformation->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_FILE)) ? PRODUCTS_OPTIONS_VALUES_TEXT_ID : (int)$data['values_id'][$i]);

          $productOptionsSortOrder = $db->Execute("SELECT DISTINCT pa.options_id, po.products_options_sort_order
                                                   FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa,
                                                        " . TABLE_PRODUCTS_OPTIONS . " po
                                                   WHERE pa.products_id = " . $productId . "
                                                   AND pa.options_id = po.products_options_id
                                                   ORDER BY products_options_sort_order");

          $products_id = zen_db_prepare_input($productId);
          $options_id = zen_db_prepare_input((int)$data['options_id']);
          $optionSortOrder = $optionInformation->fields['products_options_sort_order'];
          $value_price = zen_db_prepare_input($data['value_price']);
          $price_prefix = zen_db_prepare_input($data['price_prefix']);

          $products_options_sort_order = zen_db_prepare_input((int)$data['products_options_sort_order']);

// modified options sort order to use default if not otherwise set
          if (zen_not_null((int)$data['products_options_sort_order'])) {
            $products_options_sort_order = zen_db_prepare_input((int)$data['products_options_sort_order']);
          } else {
            $sort_order_query = $db->Execute("SELECT products_options_values_sort_order
                                              FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . "
                                              WHERE products_options_values_id = " . (int)$data['values_id'][$i]);
            $products_options_sort_order = $sort_order_query->fields['products_options_values_sort_order'];
          } // end if (zen_not_null((int)$data['products_options_sort_order'])
// end modification for sort order

          $product_attribute_is_free = zen_db_prepare_input($data['product_attribute_is_free']);
          $products_attributes_weight = zen_db_prepare_input($data['products_attributes_weight']);
          $products_attributes_weight_prefix = zen_db_prepare_input($data['products_attributes_weight_prefix']);
          $attributes_display_only = zen_db_prepare_input($data['attributes_display_only']);
          $attributes_default = zen_db_prepare_input($data['attributes_default']);
          $attributes_discounted = zen_db_prepare_input($data['attributes_discounted']);
          $attributes_price_base_included = zen_db_prepare_input($data['attributes_price_base_included']);

          $attributes_price_onetime = zen_db_prepare_input($data['attributes_price_onetime']);
          $attributes_price_factor = zen_db_prepare_input($data['attributes_price_factor']);
          $attributes_price_factor_offset = zen_db_prepare_input($data['attributes_price_factor_offset']);
          $attributes_price_factor_onetime = zen_db_prepare_input($data['attributes_price_factor_onetime']);
          $attributes_price_factor_onetime_offset = zen_db_prepare_input($data['attributes_price_factor_onetime_offset']);
          $attributes_qty_prices = zen_db_prepare_input($data['attributes_qty_prices']);
          $attributes_qty_prices_onetime = zen_db_prepare_input($data['attributes_qty_prices_onetime']);

          $attributes_price_words = zen_db_prepare_input($data['attributes_price_words']);
          $attributes_price_words_free = zen_db_prepare_input($data['attributes_price_words_free']);
          $attributes_price_letters = zen_db_prepare_input($data['attributes_price_letters']);
          $attributes_price_letters_free = zen_db_prepare_input($data['attributes_price_letters_free']);
          $attributes_required = zen_db_prepare_input($data['attributes_required']);

// add - update as record exists
// attributes images
// when set to none remove from database
// only processes image once for multiple selection of options_values_id
          if ($i == 0) {
            if (isset($data['attributes_image']) && zen_not_null($data['attributes_image']) && ($data['attributes_image'] != 'none')) {
              $attributes_image = zen_db_prepare_input($data['attributes_image']);
            } else {
              $attributes_image = '';
            }

            $attributes_image = new upload('attributes_image');
            $attributes_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
            $attributes_image->set_destination(DIR_FS_CATALOG_IMAGES . $data['img_dir']);
            if ($attributes_image->parse() && $attributes_image->save($data['overwrite'])) {
              $attributes_image_name = $data['img_dir'] . $attributes_image->filename;
            } else {
              $attributes_image_name = (isset($data['attributes_previous_image']) ? $data['attributes_previous_image'] : '');
            }
            $current_image_name = $attributes_image_name;
          } else {
            $attributes_image_name = $current_image_name;
          }

          $db->Execute("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " (products_id, options_id, options_values_id, options_values_price, price_prefix, products_options_sort_order, product_attribute_is_free, products_attributes_weight, products_attributes_weight_prefix, attributes_display_only, attributes_default, attributes_discounted, attributes_image, attributes_price_base_included, attributes_price_onetime, attributes_price_factor, attributes_price_factor_offset, attributes_price_factor_onetime, attributes_price_factor_onetime_offset, attributes_qty_prices, attributes_qty_prices_onetime, attributes_price_words, attributes_price_words_free, attributes_price_letters, attributes_price_letters_free, attributes_required)
                          VALUES ('" . (int)$products_id . "',
                                  '" . (int)$options_id . "',
                                  '" . (int)$values_id . "',
                                  '" . (float)zen_db_input($value_price) . "',
                                  '" . zen_db_input($price_prefix) . "',
                                  '" . (int)zen_db_input($products_options_sort_order) . "',
                                  '" . (int)zen_db_input($product_attribute_is_free) . "',
                                  '" . (float)zen_db_input($products_attributes_weight) . "',
                                  '" . zen_db_input($products_attributes_weight_prefix) . "',
                                  '" . (int)zen_db_input($attributes_display_only) . "',
                                  '" . (int)zen_db_input($attributes_default) . "',
                                  '" . (int)zen_db_input($attributes_discounted) . "',
                                  '" . zen_db_input($attributes_image_name) . "',
                                  '" . (int)zen_db_input($attributes_price_base_included) . "',
                                  '" . (float)zen_db_input($attributes_price_onetime) . "',
                                  '" . (float)zen_db_input($attributes_price_factor) . "',
                                  '" . (float)zen_db_input($attributes_price_factor_offset) . "',
                                  '" . (float)zen_db_input($attributes_price_factor_onetime) . "',
                                  '" . (float)zen_db_input($attributes_price_factor_onetime_offset) . "',
                                  '" . zen_db_input($attributes_qty_prices) . "',
                                  '" . zen_db_input($attributes_qty_prices_onetime) . "',
                                  '" . (float)zen_db_input($attributes_price_words) . "',
                                  '" . (int)zen_db_input($attributes_price_words_free) . "',
                                  '" . (float)zen_db_input($attributes_price_letters) . "',
                                  '" . (int)zen_db_input($attributes_price_letters_free) . "',
                                  '" . (int)zen_db_input($attributes_required) . "')");

          if (DOWNLOAD_ENABLED == 'true') {
            $products_attributes_id = $db->Insert_ID();

            $products_attributes_filename = zen_db_prepare_input($data['products_attributes_filename']);
            $products_attributes_maxdays = ($data['products_attributes_maxdays'] != '' ? (int)zen_db_prepare_input($data['products_attributes_maxdays']) : DOWNLOAD_MAX_DAYS);
            $products_attributes_maxcount = ($data['products_attributes_maxcount'] != '' ? (int)zen_db_prepare_input($data['products_attributes_maxcount']) : DOWNLOAD_MAX_COUNT);

//die( 'I am adding ' . strlen($data['products_attributes_filename']) . ' vs ' . strlen(trim($data['products_attributes_filename'])) . ' vs ' . strlen(zen_db_prepare_input($data['products_attributes_filename'])) . ' vs ' . strlen(zen_db_input($products_attributes_filename)) );
            if (zen_not_null($products_attributes_filename)) {
              $db->Execute("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " (products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount)
                            VALUES (" . (int)$products_attributes_id . ",
                                   '" . zen_db_input($products_attributes_filename) . "',
                                   '" . zen_db_input($products_attributes_maxdays) . "',
                                   '" . zen_db_input($products_attributes_maxcount) . "')");
            }
          }
          $newOptionvalues[$products_options_sort_order] = '<tr id="option-value-row-' . $products_attributes_id . '" class="option-id-' . $options_id . '">' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-a" class="align-middle">&nbsp;</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-b" class="align-middle">&nbsp;</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-c" class="align-middle">' . $products_attributes_id . '</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-d" class="align-middle">&nbsp;&nbsp;' . zen_values_name($values_id) . '</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-e" class="text-right align-middle">' . $price_prefix . '&nbsp;' . $value_price . '</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-f" class="text-right align-middle">' . $products_attributes_weight_prefix . '&nbsp;' . $products_attributes_weight . '</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-g" class="text-right align-middle">' . $products_options_sort_order . '</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-h" class="text-center align-middle">' . "\n" .
              '    <span class="attributes_display_only">' . "\n" .
              '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_display_only" class="btn btn-xs btn-default" style="opacity:0.50;" onclick="switchFlag(\'1\', \'' . $products_attributes_id . '\', \'attributes_display_only\');" title="Display Only"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>' . "\n" .
              '    </span>' . "\n" .
              '    <span class="product_attribute_is_free">' . "\n" .
              '      <button type="button" id="flag-' . $products_attributes_id . '-product_attribute_is_free" class="btn btn-xs btn-default" onclick="switchFlag(\'0\', \'' . $products_attributes_id . '\', \'product_attribute_is_free\');" title="Free"><i class="fa fa-check" aria-hidden="true"></i></button>' . "\n" .
              '    </span>' . "\n" .
              '    <span class="attributes_default">' . "\n" .
              '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_default" class="btn btn-xs btn-default" style="opacity:0.50;" onclick="switchFlag(\'1\', \'' . $products_attributes_id . '\', \'attributes_default\');" title="Default"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>' . "\n" .
              '    </span>' . "\n" .
              '    <span class="attributes_discounted">' . "\n" .
              '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_discounted" class="btn btn-xs btn-default" onclick="switchFlag(\'0\', \'' . $products_attributes_id . '\', \'attributes_discounted\');" title="Discounted"><i class="fa fa-check" aria-hidden="true"></i></button>' . "\n" .
              '    </span>' . "\n" .
              '    <span class="attributes_price_base_included">' . "\n" .
              '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_price_base_included" class="btn btn-xs btn-default" onclick="switchFlag(\'0\', \'' . $products_attributes_id . '\', \'attributes_price_base_included\');" title="Base Price"><i class="fa fa-check" aria-hidden="true"></i></button>' . "\n" .
              '    </span>' . "\n" .
              '    <span class="attributes_required">' . "\n" .
              '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_required" class="btn btn-xs btn-default" style="opacity:0.50;" onclick="switchFlag(\'1\', \'' . $products_attributes_id . '\', \'attributes_required\');" title="Required"><i class="fa fa-times" aria-hidden="true"></i></button>' . "\n" .
              '    </span>' . "\n" .
              '  </td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-i" class="text-right align-middle">$0.00 $0.00</td>' . "\n" .
              '  <td id="option-value-row-' . $products_attributes_id . '-j" class="text-center align-middle">' . "\n" .
              '    <button type="button" id="button-edit-attribute-value-' . $products_attributes_id . '" class="btn btn-sm btn-primary" data-toggle="modal" title="Edit Attribute Value" data-original-title="Edit Attribute Value" data-target="#editAttributeValueModal" onclick="editAttribute(\'' . $products_attributes_id . '\')"><i class="fa fa-edit"></i></button>' . "\n" .
              '    <button type="button" id="button-delete-attribute-value-' . $products_attributes_id . '" class="btn btn-sm btn-danger" data-toggle="modal" title="Remove Attribute Value" data-original-title="Remove Attribute Value" data-target="#deleteOptionValueModal" onclick="deleteOptionValueConfirm(\'' . $products_attributes_id . '\');"><i class="fa fa-minus-circle"></i></button>' . "\n" .
              '  </td>' . "\n" .
              '</tr>' . "\n";
        }
      }
      ksort($newOptionvalues);
      $returnData['newOptionValues'] = array_filter($newOptionvalues);
      // reset products_price_sorter for searches etc.
      zen_update_products_price_sorter($productId);

      // return new values to interface
      $newOption = '';
      $newOption .= '<tr id="option-row-' . (int)$options_id . '">' . "\n";
      $newOption .= '<td>' . "\n";
      $newOption .= '<button type="button" id="deleteOptionButton' . (int)$options_id . '" class="btn btn-sm btn-danger" data-toggle="modal" title="Remove Attribute Value" data-original-title="Remove Attribute Value" data-target="#deleteOptionModal" onclick="deleteOptionConfirm(' . (int)$options_id . ')">' . "\n";
      $newOption .= '<i class="fa fa-trash"></i>' . "\n";
      $newOption .= '</button>' . "\n";
      $newOption .= '</td>' . "\n";
      $newOption .= '<td><span style="font-weight: bold;">' . zen_options_name((int)$options_id) . '</span></td>' . "\n";
      $newOption .= '<td colspan="8"></td>' . "\n";
      $newOption .= '</tr>' . "\n";

      $returnData['newOption'] = $newOption;

      $returnData['insertSortOrderId'] = '';
      if ($productOptionsSortOrder->RecordCount() > 0) {
        foreach ($productOptionsSortOrder as $item) {
          if ($optionSortOrder < $item['products_options_sort_order']) {
            $returnData['insertSortOrderId'] = 'option-row-' . (int)$item['options_id'];
          }
        }
      }
      $returnData['optionRowId'] = 'option-row-' . (int)$options_id;

      break;
    }
  case 'saveAttribute' : {
      require(DIR_WS_CLASSES . 'currencies.php');
      $currencies = new currencies();
      $check_duplicate = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                                       WHERE products_id = " . (int)$productId . "
                                       AND options_id = " . (int)$data['options_id'] . "
                                       AND options_values_id = " . (int)$data['values_id'] . "
                                       AND products_attributes_id != " . (int)$data['attributes_id']);

      if ($check_duplicate->RecordCount() > 0) {
        // do not add duplicates give a warning
        $messageStack->add_session(ATTRIBUTE_WARNING_DUPLICATE_UPDATE . ' - ' . zen_options_name($data['options_id']) . ' : ' . zen_values_name((int)$data['values_id']), 'error');
      } else {
        // Validate options_id and options_value_id
        if (!zen_validate_options_to_options_value((int)$data['options_id'], (int)$data['values_id'])) {
          // do not add invalid match
          $messageStack->add_session(ATTRIBUTE_WARNING_INVALID_MATCH_UPDATE . ' - ' . zen_options_name((int)$data['options_id']) . ' : ' . zen_values_name((int)$data['values_id']), 'error');
        } else {
          // add the new attribute
          $productsOptionsType = $db->Execute("SELECT products_options_type
                                               FROM " . TABLE_PRODUCTS_OPTIONS . "
                                               WHERE products_options_id = " . (int)$data['options_id']);
          switch ($productsOptionsType->fields['products_options_type']) {
            case PRODUCTS_OPTIONS_TYPE_TEXT:
            case PRODUCTS_OPTIONS_TYPE_FILE:
              $values_id = PRODUCTS_OPTIONS_VALUES_TEXT_ID;
              break;
            default:
              $values_id = zen_db_prepare_input((int)$data['values_id']);
          }

          $products_id = zen_db_prepare_input((int)$productId);
          $options_id = zen_db_prepare_input((int)$data['options_id']);
          $value_price = zen_db_prepare_input($data['value_price']);
          $price_prefix = zen_db_prepare_input($data['price_prefix']);

          $products_options_sort_order = zen_db_prepare_input($data['products_options_sort_order']);
          $product_attribute_is_free = zen_db_prepare_input($data['product_attribute_is_free']);
          $products_attributes_weight = zen_db_prepare_input($data['products_attributes_weight']);
          $products_attributes_weight_prefix = zen_db_prepare_input($data['products_attributes_weight_prefix']);
          $attributes_display_only = zen_db_prepare_input($data['attributes_display_only']);
          $attributes_default = zen_db_prepare_input($data['attributes_default']);
          $attributes_discounted = zen_db_prepare_input($data['attributes_discounted']);
          $attributes_price_base_included = zen_db_prepare_input($data['attributes_price_base_included']);

          $attributes_price_onetime = zen_db_prepare_input($data['attributes_price_onetime']);
          $attributes_price_factor = zen_db_prepare_input($data['attributes_price_factor']);
          $attributes_price_factor_offset = zen_db_prepare_input($data['attributes_price_factor_offset']);
          $attributes_price_factor_onetime = zen_db_prepare_input($data['attributes_price_factor_onetime']);
          $attributes_price_factor_onetime_offset = zen_db_prepare_input($data['attributes_price_factor_onetime_offset']);
          $attributes_qty_prices = zen_db_prepare_input($data['attributes_qty_prices']);
          $attributes_qty_prices_onetime = zen_db_prepare_input($data['attributes_qty_prices_onetime']);

          $attributes_price_words = zen_db_prepare_input($data['attributes_price_words']);
          $attributes_price_words_free = zen_db_prepare_input($data['attributes_price_words_free']);
          $attributes_price_letters = zen_db_prepare_input($data['attributes_price_letters']);
          $attributes_price_letters_free = zen_db_prepare_input($data['attributes_price_letters_free']);
          $attributes_required = zen_db_prepare_input($data['attributes_required']);

          $attribute_id = zen_db_prepare_input((int)$data['attributes_id']);

// edit
// attributes images
// when set to none remove from database
          if (isset($data['attributes_image']) && zen_not_null($data['attributes_image']) && ($data['attributes_image'] != 'none')) {
            $attributes_image = zen_db_prepare_input($data['attributes_image']);
            $attributes_image_none = false;
          } else {
            $attributes_image = '';
            $attributes_image_none = true;
          }

          $attributes_image = new upload('attributes_image');
          $attributes_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
          $attributes_image->set_destination(DIR_FS_CATALOG_IMAGES . $data['img_dir']);
          if ($attributes_image->parse() && $attributes_image->save($data['overwrite'])) {
            $attributes_image_name = ($attributes_image->filename != 'none' ? ($data['img_dir'] . $attributes_image->filename) : '');
          } else {
            $attributes_image_name = ((isset($data['attributes_previous_image']) && $data['attributes_image'] != 'none') ? $data['attributes_previous_image'] : '');
          }

          if ($data['image_delete'] == 1) {
            $attributes_image_name = '';
          }
          // turned off until working
          $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                        SET attributes_image = '" . zen_db_input($attributes_image_name) . "'
                        WHERE products_attributes_id = " . (int)$attribute_id);

          $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                        SET products_id = '" . (int)$products_id . "',
                            options_id = '" . (int)$options_id . "',
                            options_values_id = '" . (int)$values_id . "',
                            options_values_price = '" . zen_db_input($value_price) . "',
                            price_prefix = '" . zen_db_input($price_prefix) . "',
                            products_options_sort_order = '" . zen_db_input($products_options_sort_order) . "',
                            product_attribute_is_free = '" . zen_db_input($product_attribute_is_free) . "',
                            products_attributes_weight = '" . zen_db_input($products_attributes_weight) . "',
                            products_attributes_weight_prefix = '" . zen_db_input($products_attributes_weight_prefix) . "',
                            attributes_display_only = '" . zen_db_input($attributes_display_only) . "',
                            attributes_default = '" . zen_db_input($attributes_default) . "',
                            attributes_discounted = '" . zen_db_input($attributes_discounted) . "',
                            attributes_price_base_included = '" . zen_db_input($attributes_price_base_included) . "',
                            attributes_price_onetime = '" . zen_db_input($attributes_price_onetime) . "',
                            attributes_price_factor = '" . zen_db_input($attributes_price_factor) . "',
                            attributes_price_factor_offset = '" . zen_db_input($attributes_price_factor_offset) . "',
                            attributes_price_factor_onetime = '" . zen_db_input($attributes_price_factor_onetime) . "',
                            attributes_price_factor_onetime_offset = '" . zen_db_input($attributes_price_factor_onetime_offset) . "',
                            attributes_qty_prices = '" . zen_db_input($attributes_qty_prices) . "',
                            attributes_qty_prices_onetime = '" . zen_db_input($attributes_qty_prices_onetime) . "',
                            attributes_price_words = '" . zen_db_input($attributes_price_words) . "',
                            attributes_price_words_free = '" . zen_db_input($attributes_price_words_free) . "',
                            attributes_price_letters = '" . zen_db_input($attributes_price_letters) . "',
                            attributes_price_letters_free = '" . zen_db_input($attributes_price_letters_free) . "',
                            attributes_required = '" . zen_db_input($attributes_required) . "'
                        WHERE products_attributes_id = " . (int)$attribute_id);

          if (DOWNLOAD_ENABLED == 'true') {
            $products_attributes_filename = zen_db_prepare_input($data['products_attributes_filename']);
            $products_attributes_maxdays = ($data['products_attributes_maxdays'] != '' ? (int)zen_db_prepare_input($data['products_attributes_maxdays']) : DOWNLOAD_MAX_DAYS);
            $products_attributes_maxcount = ($data['products_attributes_maxcount'] != '' ? (int)zen_db_prepare_input($data['products_attributes_maxcount']) : DOWNLOAD_MAX_COUNT);

            if (zen_not_null($products_attributes_filename)) {
              $db->Execute("REPLACE INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                            SET products_attributes_id = " . (int)$attribute_id . ",
                                products_attributes_filename = '" . zen_db_input($products_attributes_filename) . "',
                                products_attributes_maxdays = '" . zen_db_input($products_attributes_maxdays) . "',
                                products_attributes_maxcount = '" . zen_db_input($products_attributes_maxcount) . "'");
            } else {
              $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                            WHERE products_attributes_id = " . (int)$attribute_id);
            }
          }
        }
      }

      // reset products_price_sorter for searches etc.
      zen_update_products_price_sorter((int)$productId);

      if (DOWNLOAD_ENABLED == 'true') {
        $attributeDownloadQuery = "SELECT products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                                   FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                                   WHERE products_attributes_id = " . (int)$data['attributes_id'];
        $attributeDownload = $db->Execute($attributeDownloadQuery);
        $filename_is_missing = '';
        if (!file_exists(DIR_FS_DOWNLOAD . $attributeDownload->fields['products_attributes_filename'])) {
          $filename_is_missing = zen_image(DIR_WS_IMAGES . 'icon_status_red.gif');
        } else {
          $filename_is_missing = zen_image(DIR_WS_IMAGES . 'icon_status_green.gif');
        }
      }

      $productCheck = $db->Execute("SELECT products_tax_class_id
                                    FROM " . TABLE_PRODUCTS . "
                                    WHERE products_id = " . $productId . "
                                    LIMIT 1");

      // calculate current total attribute price
      $attributes_price_final = zen_get_attributes_price_final($data['attributes_id'], 1, $data, 'false');
      $attributes_price_final_value = $attributes_price_final;
      $attributes_price_final = $currencies->display_price($attributes_price_final, zen_get_tax_rate($productCheck->fields['products_tax_class_id']), 1);
      $attributes_price_final_onetime = zen_get_attributes_price_final_onetime($data['attributes_id'], 1, $data);
      $attributes_price_final_onetime = $currencies->display_price($attributes_price_final_onetime, zen_get_tax_rate($productCheck->fields['products_tax_class_id']), 1);
      $new_attributes_price = '';
      if ($data['attributes_discounted']) {
        $new_attributes_price = zen_get_attributes_price_final($data['attributes_id'], 1, '', 'false');
        $new_attributes_price = zen_get_discount_calc($productId, true, $new_attributes_price);
        if ($new_attributes_price != $attributes_price_final_value) {
          $new_attributes_price = '|' . $currencies->display_price($new_attributes_price, zen_get_tax_rate($pInfo->products_tax_class_id), 1);
        } else {
          $new_attributes_price = '';
        }
      }

      $optionValuesRow['a'] = '&nbsp;';
      $optionValuesRow['b'] = '&nbsp;';
      $optionValuesRow['c'] = $data['attributes_id'];
      $optionValuesRow['d'] = ((DOWNLOAD_ENABLED == 'true' && $products_attributes_filename != '') ? '<div class="smallText">' . $filename_is_missing . '&nbsp;' . $products_attributes_filename . '&nbsp;-&nbsp;' . TABLE_TEXT_MAX_DAYS_SHORT . '&nbsp;' . $products_attributes_maxdays . '&nbsp;-&nbsp;' . TABLE_TEXT_MAX_COUNT_SHORT . '&nbsp;' . $products_attributes_maxcount . '</div>' : '') . ($attributes_image_name != '' ? zen_image(DIR_WS_IMAGES . 'icon_status_yellow.gif') . '&nbsp;' : '&nbsp;&nbsp;') . zen_values_name($data['values_id']);
      $optionValuesRow['e'] = $price_prefix . '&nbsp;' . $value_price;
      $optionValuesRow['f'] = $products_attributes_weight_prefix . '&nbsp;' . $products_attributes_weight;
      $optionValuesRow['g'] = $products_options_sort_order;
      if ($attributes_display_only == '0') {
        $optionValuesRow['h']['attributes_display_only'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_display_only" class="btn btn-xs btn-default" style="opacity:0.50;" onClick="switchFlag(\'1\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_display_only\');" title="' . LEGEND_ATTRIBUTES_DISPLAY_ONLY . '"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>';
      } else {
        $optionValuesRow['h']['attributes_display_only'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_display_only" class="btn btn-xs btn-default" onClick="switchFlag(\'0\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_display_only\');" title="' . LEGEND_ATTRIBUTES_DISPLAY_ONLY . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
      }
      if ($product_attribute_is_free == '0') {
        $optionValuesRow['h']['product_attribute_is_free'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-product_attribute_is_free" class="btn btn-xs btn-default" style="opacity:0.50;" onClick="switchFlag(\'1\', \'' . $attribute['products_attributes_id'] . '\', \'product_attribute_is_free\');" title="' . LEGEND_ATTRIBUTES_IS_FREE . '"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>';
      } else {
        $optionValuesRow['h']['product_attribute_is_free'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-product_attribute_is_free" class="btn btn-xs btn-default" onClick="switchFlag(\'0\', \'' . $attribute['products_attributes_id'] . '\', \'product_attribute_is_free\');" title="' . LEGEND_ATTRIBUTES_IS_FREE . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
      }
      if ($attributes_default == '0') {
        $optionValuesRow['h']['attributes_default'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_default" class="btn btn-xs btn-default" style="opacity:0.50;" onClick="switchFlag(\'1\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_default\');" title="' . LEGEND_ATTRIBUTES_DEFAULT . '"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>';
      } else {
        $optionValuesRow['h']['attributes_default'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_default" class="btn btn-xs btn-default" onClick="switchFlag(\'0\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_default\');" title="' . LEGEND_ATTRIBUTES_DEFAULT . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
      }
      if ($attributes_discounted == '0') {
        $optionValuesRow['h']['attributes_discounted'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_discounted" class="btn btn-xs btn-default" style="opacity:0.50;" onClick="switchFlag(\'1\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_discounted\');" title="' . LEGEND_ATTRIBUTE_IS_DISCOUNTED . '"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>';
      } else {
        $optionValuesRow['h']['attributes_discounted'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_discounted" class="btn btn-xs btn-default" onClick="switchFlag(\'0\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_discounted\');" title="' . LEGEND_ATTRIBUTE_IS_DISCOUNTED . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
      }
      if ($attributes_price_base_included == '0') {
        $optionValuesRow['h']['attributes_price_base_included'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_price_base_included" class="btn btn-xs btn-default" style="opacity:0.50;" onClick="switchFlag(\'1\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_price_base_included\');" title="' . LEGEND_ATTRIBUTE_PRICE_BASE_INCLUDED . '"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>';
      } else {
        $optionValuesRow['h']['attributes_price_base_included'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_price_base_included" class="btn btn-xs btn-default" onClick="switchFlag(\'0\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_price_base_included\');" title="' . LEGEND_ATTRIBUTE_PRICE_BASE_INCLUDED . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
      }
      if ($attributes_required == '0') {
        $optionValuesRow['h']['attributes_required'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_required" class="btn btn-xs btn-default" style="opacity:0.50;" onClick="switchFlag(\'1\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_required\');" title="' . LEGEND_ATTRIBUTES_REQUIRED . '"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>';
      } else {
        $optionValuesRow['h']['attributes_required'] = '<button type="button" id="flag-' . $attribute['products_attributes_id'] . '-attributes_required" class="btn btn-xs btn-default" onClick="switchFlag(\'0\', \'' . $attribute['products_attributes_id'] . '\', \'attributes_required\');" title="' . LEGEND_ATTRIBUTES_REQUIRED . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
      }
      $optionValuesRow['i'] = $attributes_price_final . $new_attributes_price . ' ' . $attributes_price_final_onetime;
      $optionValuesRow['j'] = '';

      $returnData['optionValuesRow'] = $optionValuesRow;
      $returnData['options_id'] = $data['options_id'];
      $returnData['values_id'] = $data['values_id'];
      $returnData['attribute_id'] = $data['attributes_id'];
      break;
    }
  case 'deleteOptionConfirm' : {
      $modalContent = '';
      $modalContent .= '<p class="danger">' . TEXT_DELETE_ATTRIBUTES_OPTION_NAME_VALUES . '</p>';
      $modalContent .= '<div class="form-group">';
      $modalContent .= '<p class="form-control">' . TEXT_INFO_PRODUCT_NAME . zen_get_products_name($productId, $_SESSION['languages_id']) . '</p>';
      $modalContent .= '<p class="form-control">' . TEXT_INFO_PRODUCTS_OPTION_NAME . zen_options_name($data['options_id']) . '</p>';
      $modalContent .= '<p class="form-control">' . TEXT_INFO_PRODUCTS_OPTION_ID . $data['options_id'] . '</p>';
      $modalContent .= '</div>';
      $modalContent .= zen_draw_hidden_field('products_id', $data['products_id']);
      $modalContent .= zen_draw_hidden_field('options_id', $data['options_id']);
      $modalContent .= zen_draw_hidden_field('view', 'deleteOption');
      $returnData['modalContent'] = $modalContent;
      break;
    }
  case 'deleteOption' : {
      $getAttributesOptionsId = $db->Execute("SELECT products_attributes_id
                                                    FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                                                    WHERE products_id='" . (int)$productId . "'
                                                    AND options_id='" . (int)$data['options_id'] . "'");
      foreach ($getAttributesOptionsId as $attributesOptionsId) {
// remove any attached downloads
        $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                      WHERE products_attributes_id= '" . (int)$attributesOptionsId['products_attributes_id'] . "'");
// remove all option values
        $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                      WHERE products_id='" . (int)$productId . "'
                      AND options_id='" . (int)$data['options_id'] . "'");
      }

      $messageStack->add_session(SUCCESS_ATTRIBUTES_DELETED_OPTION_NAME_VALUES . ' ID#' . zen_options_name((int)$data['options_id']), 'success');

      // reset products_price_sorter for searches etc.
      zen_update_products_price_sorter((int)$data['options_id']);

      $returnData['optionId'] = $data['options_id'];
      break;
    }
  case 'deleteOptionValueConfirm' : {

      $attribute = $db->Execute("SELECT options_id,options_values_id
                               FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                               WHERE products_attributes_id = " . (int)$data['attributes_id']);
      $returnData['db'] = $attribute;
      $modalContent = '';
      $modalContent .= '<p class="danger">' . TEXT_DELETE_ATTRIBUTES_VALUE . '</p>';
      $modalContent .= '<div class="form-group">';
      $modalContent .= '<p class="form-control">' . TEXT_INFO_PRODUCT_NAME . zen_get_products_name($productId, $_SESSION['languages_id']) . '</p>';
      $modalContent .= '<p class="form-control">' . TEXT_INFO_PRODUCTS_VALUE_NAME . zen_options_name($attribute->fields['options_id']) . ' => ' . zen_values_name($attribute->fields['options_values_id']) . '</p>';
      $modalContent .= '</div>';
      $modalContent .= zen_draw_hidden_field('attributes_id', $data['attributes_id']);
      $modalContent .= zen_draw_hidden_field('view', 'deleteOptionValue');
      $returnData['modalContent'] = $modalContent;
      break;
    }
  case 'deleteOptionValue' : {
      $attribute_id = zen_db_prepare_input((int)$data['attributes_id']);

      $zco_notifier->notify('NOTIFY_ATTRIBUTE_CONTROLLER_DELETE_ATTRIBUTE', array('attribute_id' => $attribute_id), $attribute_id);

      $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                    WHERE products_attributes_id = " . (int)$attribute_id);

      $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                    WHERE products_attributes_id = " . (int)$attribute_id);

      // reset products_price_sorter for searches etc.
      zen_update_products_price_sorter($productId);
      $returnData['attributesId'] = $data['attributes_id'];
      break;
    }
  case 'switchFlag' : {
      if ($data['flag'] == '1') {
        $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                      SET " . $data['flag_name'] . " = '1'
                      WHERE products_id = " . (int)$productId . "
                      AND products_attributes_id = " . (int)$data['attributes_id']);

        $returnData['button'] = '<button type="button" id="flag-' . $data['attributes_id'] . '-' . $data['flag_name'] . '" class="btn btn-xs btn-default" onClick="switchFlag(\'0\', \'' . $data['attributes_id'] . '\', \'' . $data['flag_name'] . '\');return false;" title="' . LEGEND_ATTRIBUTES_DISPLAY_ONLY . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
      } else {
        $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                      SET " . $data['flag_name'] . " = '0'
                      WHERE products_id = " . (int)$productId . "
                      AND products_attributes_id = " . (int)$data['attributes_id']);

        $returnData['button'] = '<button type="button" id="flag-' . $data['attributes_id'] . '-' . $data['flag_name'] . '" class="btn btn-xs btn-default" style="opacity:0.50;" onClick="switchFlag(\'1\', \'' . $data['attributes_id'] . '\', \'' . $data['flag_name'] . '\')" title="' . LEGEND_ATTRIBUTES_DISPLAY_ONLY . '"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>';
      }
      break;
    }
  case 'messageStack': {
      if ($messageStack->size > 0) {
        $returnData['modalMessageStack'] = $messageStack->output();
      }
      break;
    }
}

echo json_encode($returnData);
require(DIR_WS_INCLUDES . 'application_bottom.php');
