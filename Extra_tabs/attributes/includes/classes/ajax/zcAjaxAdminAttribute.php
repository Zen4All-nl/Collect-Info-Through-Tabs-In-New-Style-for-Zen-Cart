<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class zcAjaxAdminAttribute extends base {

  public function updateValueDropDown() {
    global $db;
    $data = new objectInfo($_POST);

    $optionValuesQuery = "SELECT povpo.products_options_values_id,
                                 pov.products_options_values_name
                          FROM " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " povpo,
                               " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
                          WHERE povpo.products_options_id = " . (int)$data->options_id . "
                          AND povpo.products_options_values_id = pov.products_options_values_id
                          AND pov.language_id = " . (int)$_SESSION['languages_id'] . "
                          ORDER BY pov.products_options_values_name";

    $optionValues = $db->Execute($optionValuesQuery);

    $valuesDropDownArray = '';
    foreach ($optionValues as $optionValue) {
      $text = $optionValue['products_options_values_name'] . ($optionValue['products_options_values_id'] == 0 ? '/UPLOAD FILE' : '') . ' [ #' . $optionValue['products_options_values_id'] . ' ] ';
      $valuesDropDownArray .= '<option value="' . zen_output_string($optionValue['products_options_values_id']) . '">' . zen_output_string($text, array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>' . PHP_EOL;
    }
    return([
      'valuesDropDownArray' => $valuesDropDownArray]);
  }

  // update by product
  public function updateAttributeSort() {
    global $messageStack;
    $data = new objectInfo($_POST);

    if (!zen_has_product_attributes($data->products_id, 'false')) {
      $messageStack->add_session(SUCCESS_PRODUCT_UPDATE_SORT_NONE . $data->products_id . ' ' . zen_get_products_name($data->products_id, $_SESSION['languages_id']), 'error');
    } else {
      zen_update_attributes_products_option_values_sort_order($data->products_id);
      $messageStack->add_session(SUCCESS_PRODUCT_UPDATE_SORT . $data->products_id . ' ' . zen_get_products_name($data->products_id, $_SESSION['languages_id']), 'success');
    }
  }

  public function productCopyToProduct() {
    $data = new objectInfo($_POST);

    $copy_attributes_delete_first = ($data->copy_attributes == 'copy_attributes_delete' ? '1' : '0');
    $copy_attributes_duplicates_skipped = ($data->copy_attributes == 'copy_attributes_ignore' ? '1' : '0');
    $copy_attributes_duplicates_overwrite = ($data->copy_attributes == 'copy_attributes_update' ? '1' : '0');
    zen_copy_products_attributes($data->products_id, (int)$data->products_update_id);
  }

  public function productCopyToCategory() {
    global $db, $messageStack;
    $data = new objectInfo($_POST);

    $copy_attributes_delete_first = ($data->copy_attributes == 'copy_attributes_delete' ? '1' : '0');
    $copy_attributes_duplicates_skipped = ($data->copy_attributes == 'copy_attributes_ignore' ? '1' : '0');
    $copy_attributes_duplicates_overwrite = ($data->copy_attributes == 'copy_attributes_update' ? '1' : '0');
    if ($data->categories_update_id == '') {
      $messageStack->add_session(WARNING_PRODUCT_COPY_TO_CATEGORY_NONE . ' ID#' . $data->products_id, 'warning');
    } else {
      $copy_to_category = $db->Execute("SELECT products_id
                                        FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                        WHERE categories_id = " . (int)$data->categories_update_id);
      foreach ($copy_to_category as $copy) {
        zen_copy_products_attributes((int)$data->products_id, (int)$copy['products_id']);
      }
    }
  }

  public function addAttribute() {
    $data = new objectInfo($_POST);
    return([
      'products_id' => (int)$data->products_id]);
  }

  public function editAttribute() {
    global $db;
    $data = new objectInfo($_POST);

    $attributeId = (int)$data->attribute_id;
    $attributeValuesQuery = "SELECT pa.*
                             FROM (" . TABLE_PRODUCTS_ATTRIBUTES . " pa
                             LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON pa.options_id = po.products_options_id
                               AND po.language_id = " . (int)$_SESSION['languages_id'] . ")
                             WHERE products_id = " . (int)$data->products_id . "
                             AND products_attributes_id = " . (int)$attributeId . "
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

    if (ATTRIBUTES_ENABLED_IMAGES == 'true') {

      $attributeImage = ($attributeValuesArray['attributes_image'] != '' ? zen_image(DIR_WS_CATALOG_IMAGES . $attributeValuesArray['attributes_image'], $attributeValuesArray['attributes_image'], '', '', 'class="img-thumbnail"') : '');
    }
    $optionValues = $db->Execute("SELECT pov.products_options_values_id, pov.products_options_values_name
                                  FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
                                  LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " povtpo ON pov.products_options_values_id = povtpo.products_options_values_id
                                  WHERE pov.language_id = " . (int)$_SESSION['languages_id'] . "
                                  AND povtpo.products_options_id = " . (int)$attributeValuesArray['options_id'] . "
                                  ORDER BY pov.products_options_values_name");

    $optionValuesArray = [];
    foreach ($optionValues as $value) {
      $optionValuesArray[] = [
        'id' => $value['products_options_values_id'],
        'text' => $value['products_options_values_name']
      ];
    }
    $optionValuesPullDown = zen_draw_pull_down_menu('values_id', $optionValuesArray, $attributeValuesArray['options_values_id'], 'size="10" class="form-control"');
    // set flag radio's

    return([
      'attributeImage' => $attributeImage,
      'attributeValuesArray' => $attributeValuesArray,
      'optionValuesPullDown' => $optionValuesPullDown]);
  }

  public function insertAttribute() {
    global $db, $messageStack;
    $data = new objectInfo($_POST);

    $current_image_name = '';
    $newOptionValues[] = array();
    for ($i = 0; $i < sizeof($data->values_id); $i++) {
// check for duplicate and block them
      $check_duplicate = $db->Execute("SELECT *
                                       FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                                       WHERE products_id = " . (int)$data->products_id . "
                                       AND options_id = " . (int)$data->options_id . "
                                       AND options_values_id = " . (int)$data->values_id[$i]);
      if ($check_duplicate->RecordCount() > 0) {
        // do not add duplicates -- give a warning
        $messageStack->add_session(ATTRIBUTE_WARNING_DUPLICATE . ' - ' . zen_options_name((int)$data->options_id) . ' : ' . zen_values_name((int)$data->values_id[$i]), 'error');
      } else {
// For TEXT and FILE option types, ignore option value entered by administrator and use PRODUCTS_OPTIONS_VALUES_TEXT instead.
        $optionInformation = $db->Execute("SELECT products_options_type, products_options_sort_order
                                           FROM " . TABLE_PRODUCTS_OPTIONS . "
                                           WHERE products_options_id = " . (int)$data->options_id);
        $values_id = zen_db_prepare_input((($optionInformation->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_TEXT) || ( $optionInformation->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_FILE)) ? PRODUCTS_OPTIONS_VALUES_TEXT_ID : (int)$data->values_id[$i]);

        $productOptionsSortOrder = $db->Execute("SELECT DISTINCT pa.options_id, po.products_options_sort_order
                                                 FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa,
                                                      " . TABLE_PRODUCTS_OPTIONS . " po
                                                 WHERE pa.products_id = " . (int)$data->products_id . "
                                                 AND pa.options_id = po.products_options_id
                                                 ORDER BY products_options_sort_order");

        $products_id = zen_db_prepare_input($data->products_id);
        $options_id = zen_db_prepare_input((int)$data->options_id);
        $optionSortOrder = $optionInformation->fields['products_options_sort_order'];
        $value_price = zen_db_prepare_input($data->value_price);
        $price_prefix = zen_db_prepare_input($data->price_prefix);

        $products_options_sort_order = zen_db_prepare_input((int)$data->products_options_sort_order);

// modified options sort order to use default if not otherwise set
        if (zen_not_null((int)$data->products_options_sort_order)) {
          $products_options_sort_order = zen_db_prepare_input((int)$data->products_options_sort_order);
        } else {
          $sort_order_query = $db->Execute("SELECT products_options_values_sort_order
                                            FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . "
                                            WHERE products_options_values_id = " . (int)$data->values_id[$i]);
          $products_options_sort_order = $sort_order_query->fields['products_options_values_sort_order'];
        }
// end modification for sort order

        $product_attribute_is_free = zen_db_prepare_input($data->product_attribute_is_free);
        $products_attributes_weight = zen_db_prepare_input($data->products_attributes_weight);
        $products_attributes_weight_prefix = zen_db_prepare_input($data->products_attributes_weight_prefix);
        $attributes_display_only = zen_db_prepare_input($data->attributes_display_only);
        $attributes_default = zen_db_prepare_input($data->attributes_default);
        $attributes_discounted = zen_db_prepare_input($data->attributes_discounted);
        $attributes_price_base_included = zen_db_prepare_input($data->attributes_price_base_included);

        $attributes_price_onetime = zen_db_prepare_input($data->attributes_price_onetime);
        $attributes_price_factor = zen_db_prepare_input($data->attributes_price_factor);
        $attributes_price_factor_offset = zen_db_prepare_input($data->attributes_price_factor_offset);
        $attributes_price_factor_onetime = zen_db_prepare_input($data->attributes_price_factor_onetime);
        $attributes_price_factor_onetime_offset = zen_db_prepare_input($data->attributes_price_factor_onetime_offset);
        $attributes_qty_prices = zen_db_prepare_input($data->attributes_qty_prices);
        $attributes_qty_prices_onetime = zen_db_prepare_input($data->attributes_qty_prices_onetime);

        $attributes_price_words = zen_db_prepare_input($data->attributes_price_words);
        $attributes_price_words_free = zen_db_prepare_input($data->attributes_price_words_free);
        $attributes_price_letters = zen_db_prepare_input($data->attributes_price_letters);
        $attributes_price_letters_free = zen_db_prepare_input($data->attributes_price_letters_free);
        $attributes_required = zen_db_prepare_input($data->attributes_required);

// add - update as record exists
// attributes images
// when set to none remove from database
// only processes image once for multiple selection of options_values_id
        if ($i == 0) {
          if (isset($data->attributes_image) && zen_not_null($data->attributes_image) && ($data->attributes_image != 'none')) {
            $attributes_image = zen_db_prepare_input($data->attributes_image);
          } else {
            $attributes_image = '';
          }

          $attributes_image = new upload('attributes_image');
          $attributes_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
          $attributes_image->set_destination(DIR_FS_CATALOG_IMAGES . $data->img_dir);
          if ($attributes_image->parse() && $attributes_image->save($data->overwrite)) {
            $attributes_image_name = $data->img_dir . $attributes_image->filename;
          } else {
            $attributes_image_name = (isset($data->attributes_previous_image) ? $data->attributes_previous_image : '');
          }
          $current_image_name = $attributes_image_name;
        } else {
          $attributes_image_name = $current_image_name;
        }

        $db->Execute("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " (products_id, options_id, options_values_id, options_values_price, price_prefix, products_options_sort_order, product_attribute_is_free, products_attributes_weight, products_attributes_weight_prefix, attributes_display_only, attributes_default, attributes_discounted, attributes_image, attributes_price_base_included, attributes_price_onetime, attributes_price_factor, attributes_price_factor_offset, attributes_price_factor_onetime, attributes_price_factor_onetime_offset, attributes_qty_prices, attributes_qty_prices_onetime, attributes_price_words, attributes_price_words_free, attributes_price_letters, attributes_price_letters_free, attributes_required)
                      VALUES (" . (int)$products_id . ",
                              " . (int)$options_id . ",
                              " . (int)$values_id . ",
                              '" . (float)zen_db_input($value_price) . "',
                              '" . zen_db_input($price_prefix) . "',
                              " . (int)zen_db_input($products_options_sort_order) . ",
                              " . (int)zen_db_input($product_attribute_is_free) . ",
                              '" . (float)zen_db_input($products_attributes_weight) . "',
                              '" . zen_db_input($products_attributes_weight_prefix) . "',
                              " . (int)zen_db_input($attributes_display_only) . ",
                              " . (int)zen_db_input($attributes_default) . ",
                              " . (int)zen_db_input($attributes_discounted) . ",
                              '" . zen_db_input($attributes_image_name) . "',
                              " . (int)zen_db_input($attributes_price_base_included) . ",
                              '" . (float)zen_db_input($attributes_price_onetime) . "',
                              '" . (float)zen_db_input($attributes_price_factor) . "',
                              '" . (float)zen_db_input($attributes_price_factor_offset) . "',
                              '" . (float)zen_db_input($attributes_price_factor_onetime) . "',
                              '" . (float)zen_db_input($attributes_price_factor_onetime_offset) . "',
                              '" . zen_db_input($attributes_qty_prices) . "',
                              '" . zen_db_input($attributes_qty_prices_onetime) . "',
                              '" . (float)zen_db_input($attributes_price_words) . "',
                              " . (int)zen_db_input($attributes_price_words_free) . ",
                              '" . (float)zen_db_input($attributes_price_letters) . "',
                              " . (int)zen_db_input($attributes_price_letters_free) . ",
                              " . (int)zen_db_input($attributes_required) . ")");

        if (DOWNLOAD_ENABLED == 'true') {
          $products_attributes_id = $db->Insert_ID();

          $products_attributes_filename = zen_db_prepare_input($data->products_attributes_filename);
          $products_attributes_maxdays = ($data->products_attributes_maxdays != '' ? (int)zen_db_prepare_input($data->products_attributes_maxdays) : DOWNLOAD_MAX_DAYS);
          $products_attributes_maxcount = ($data->products_attributes_maxcount != '' ? (int)zen_db_prepare_input($data->products_attributes_maxcount) : DOWNLOAD_MAX_COUNT);

          if (zen_not_null($products_attributes_filename)) {
            $db->Execute("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " (products_attributes_id, products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount)
                          VALUES (" . (int)$products_attributes_id . ",
                                 '" . zen_db_input($products_attributes_filename) . "',
                                 '" . zen_db_input($products_attributes_maxdays) . "',
                                 '" . zen_db_input($products_attributes_maxcount) . "')");
          }
        }
        $newOptionValues[$products_options_sort_order] = '<tr id="option-value-row-' . $products_attributes_id . '" class="option-id-' . $options_id . '">' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-a" class="align-middle">&nbsp;</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-b" class="align-middle">&nbsp;</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-c" class="align-middle">' . $products_attributes_id . '</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-d" class="align-middle">&nbsp;&nbsp;' . zen_values_name($values_id) . '</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-e" class="text-right align-middle">' . $price_prefix . '&nbsp;' . $value_price . '</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-f" class="text-right align-middle">' . $products_attributes_weight_prefix . '&nbsp;' . $products_attributes_weight . '</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-g" class="text-right align-middle">' . $products_options_sort_order . '</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-h" class="text-center align-middle">' . PHP_EOL .
            '    <span class="attributes_display_only">' . PHP_EOL .
            '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_display_only" class="btn btn-xs btn-default" style="opacity:0.50;" onclick="switchFlag(\'1\', \'' . $products_attributes_id . '\', \'attributes_display_only\');" title="Display Only"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>' . PHP_EOL .
            '    </span>' . PHP_EOL .
            '    <span class="product_attribute_is_free">' . PHP_EOL .
            '      <button type="button" id="flag-' . $products_attributes_id . '-product_attribute_is_free" class="btn btn-xs btn-default" onclick="switchFlag(\'0\', \'' . $products_attributes_id . '\', \'product_attribute_is_free\');" title="Free"><i class="fa fa-check" aria-hidden="true"></i></button>' . PHP_EOL .
            '    </span>' . PHP_EOL .
            '    <span class="attributes_default">' . PHP_EOL .
            '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_default" class="btn btn-xs btn-default" style="opacity:0.50;" onclick="switchFlag(\'1\', \'' . $products_attributes_id . '\', \'attributes_default\');" title="Default"><i class="fa fa-times" aria-hidden="true" style="color:#f00;"></i></button>' . PHP_EOL .
            '    </span>' . PHP_EOL .
            '    <span class="attributes_discounted">' . PHP_EOL .
            '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_discounted" class="btn btn-xs btn-default" onclick="switchFlag(\'0\', \'' . $products_attributes_id . '\', \'attributes_discounted\');" title="Discounted"><i class="fa fa-check" aria-hidden="true"></i></button>' . PHP_EOL .
            '    </span>' . PHP_EOL .
            '    <span class="attributes_price_base_included">' . PHP_EOL .
            '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_price_base_included" class="btn btn-xs btn-default" onclick="switchFlag(\'0\', \'' . $products_attributes_id . '\', \'attributes_price_base_included\');" title="Base Price"><i class="fa fa-check" aria-hidden="true"></i></button>' . PHP_EOL .
            '    </span>' . PHP_EOL .
            '    <span class="attributes_required">' . PHP_EOL .
            '      <button type="button" id="flag-' . $products_attributes_id . '-attributes_required" class="btn btn-xs btn-default" style="opacity:0.50;" onclick="switchFlag(\'1\', \'' . $products_attributes_id . '\', \'attributes_required\');" title="Required"><i class="fa fa-times" aria-hidden="true"></i></button>' . PHP_EOL .
            '    </span>' . PHP_EOL .
            '  </td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-i" class="text-right align-middle">$0.00 $0.00</td>' . PHP_EOL .
            '  <td id="option-value-row-' . $products_attributes_id . '-j" class="text-center align-middle">' . PHP_EOL .
            '    <button type="button" id="button-edit-attribute-value-' . $products_attributes_id . '" class="btn btn-sm btn-primary" data-toggle="modal" title="Edit Attribute Value" data-original-title="Edit Attribute Value" data-target="#editAttributeValueModal" onclick="editAttribute(\'' . $products_attributes_id . '\')"><i class="fa fa-edit"></i></button>' . PHP_EOL .
            '    <button type="button" id="button-delete-attribute-value-' . $products_attributes_id . '" class="btn btn-sm btn-danger" data-toggle="modal" title="Remove Attribute Value" data-original-title="Remove Attribute Value" data-target="#deleteOptionValueModal" onclick="deleteOptionValueConfirm(\'' . $products_attributes_id . '\');"><i class="fa fa-minus-circle"></i></button>' . PHP_EOL .
            '  </td>' . PHP_EOL .
            '</tr>' . PHP_EOL;
      }
    }
    ksort($newOptionValues);
    $newOptionValuesReturn = array_filter($newOptionValues);
    // reset products_price_sorter for searches etc.
    zen_update_products_price_sorter($data->products_id);

    // return new values to interface
    $newOption = '';
    $newOption .= '<tr id="option-row-' . (int)$options_id . '">' . PHP_EOL;
    $newOption .= '<td>' . PHP_EOL;
    $newOption .= '<button type="button" id="deleteOptionButton' . (int)$options_id . '" class="btn btn-sm btn-danger" data-toggle="modal" title="Remove Attribute Value" data-original-title="Remove Attribute Value" data-target="#deleteOptionModal" onclick="deleteOptionConfirm(' . (int)$options_id . ')">' . PHP_EOL;
    $newOption .= '<i class="fa fa-trash"></i>' . PHP_EOL;
    $newOption .= '</button>' . PHP_EOL;
    $newOption .= '</td>' . PHP_EOL;
    $newOption .= '<td><span style="font-weight: bold;">' . zen_options_name((int)$options_id) . '</span></td>' . PHP_EOL;
    $newOption .= '<td colspan="8"></td>' . PHP_EOL;
    $newOption .= '</tr>' . PHP_EOL;

    $insertSortOrderId = '';
    if ($productOptionsSortOrder->RecordCount() > 0) {
      foreach ($productOptionsSortOrder as $item) {
        if ($optionSortOrder < $item['products_options_sort_order']) {
          $insertSortOrderId = 'option-row-' . (int)$item['options_id'];
        }
      }
    }
    $optionRowId = 'option-row-' . (int)$options_id;
    return([
      'newOptionValues' => $newOptionValuesReturn,
      'newOption' => $newOption,
      'optionRowId' => $optionRowId,
      'insertSortOrderId' => $insertSortOrderId,
    ]);
  }

  public function saveAttribute() {
    global $db, $messageStack;
    $data = new objectInfo($_POST);

    require(DIR_WS_CLASSES . 'currencies.php');
    $currencies = new currencies();
    $check_duplicate = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                                     WHERE products_id = " . (int)$data->product_id . "
                                     AND options_id = " . (int)$data->options_id . "
                                     AND options_values_id = " . (int)$data->values_id . "
                                     AND products_attributes_id != " . (int)$data->attributes_id);

    if ($check_duplicate->RecordCount() > 0) {
      // do not add duplicates give a warning
      $messageStack->add_session(ATTRIBUTE_WARNING_DUPLICATE_UPDATE . ' - ' . zen_options_name($data->options_id) . ' : ' . zen_values_name((int)$data->values_id), 'error');
    } else {
      // Validate options_id and options_value_id
      if (!zen_validate_options_to_options_value((int)$data->options_id, (int)$data->values_id)) {
        // do not add invalid match
        $messageStack->add_session(ATTRIBUTE_WARNING_INVALID_MATCH_UPDATE . ' - ' . zen_options_name((int)$data->options_id) . ' : ' . zen_values_name((int)$data->values_id), 'error');
      } else {
        // add the new attribute
        $productsOptionsType = $db->Execute("SELECT products_options_type
                                             FROM " . TABLE_PRODUCTS_OPTIONS . "
                                             WHERE products_options_id = " . (int)$data->options_id);
        switch ($productsOptionsType->fields['products_options_type']) {
          case PRODUCTS_OPTIONS_TYPE_TEXT:
          case PRODUCTS_OPTIONS_TYPE_FILE:
            $values_id = PRODUCTS_OPTIONS_VALUES_TEXT_ID;
            break;
          default:
            $values_id = zen_db_prepare_input((int)$data->values_id);
        }

        $products_id = zen_db_prepare_input((int)$data->product_id);
        $options_id = zen_db_prepare_input((int)$data->options_id);
        $value_price = zen_db_prepare_input($data->value_price);
        $price_prefix = zen_db_prepare_input($data->price_prefix);

        $products_options_sort_order = zen_db_prepare_input($data->products_options_sort_order);
        $product_attribute_is_free = zen_db_prepare_input($data->product_attribute_is_free);
        $products_attributes_weight = zen_db_prepare_input($data->products_attributes_weight);
        $products_attributes_weight_prefix = zen_db_prepare_input($data->products_attributes_weight_prefix);
        $attributes_display_only = zen_db_prepare_input($data->attributes_display_only);
        $attributes_default = zen_db_prepare_input($data->attributes_default);
        $attributes_discounted = zen_db_prepare_input($data->attributes_discounted);
        $attributes_price_base_included = zen_db_prepare_input($data->attributes_price_base_included);

        $attributes_price_onetime = zen_db_prepare_input($data->attributes_price_onetime);
        $attributes_price_factor = zen_db_prepare_input($data->attributes_price_factor);
        $attributes_price_factor_offset = zen_db_prepare_input($data->attributes_price_factor_offset);
        $attributes_price_factor_onetime = zen_db_prepare_input($data->attributes_price_factor_onetime);
        $attributes_price_factor_onetime_offset = zen_db_prepare_input($data->attributes_price_factor_onetime_offset);
        $attributes_qty_prices = zen_db_prepare_input($data->attributes_qty_prices);
        $attributes_qty_prices_onetime = zen_db_prepare_input($data->attributes_qty_prices_onetime);

        $attributes_price_words = zen_db_prepare_input($data->attributes_price_words);
        $attributes_price_words_free = zen_db_prepare_input($data->attributes_price_words_free);
        $attributes_price_letters = zen_db_prepare_input($data->attributes_price_letters);
        $attributes_price_letters_free = zen_db_prepare_input($data->attributes_price_letters_free);
        $attributes_required = zen_db_prepare_input($data->attributes_required);

        $attribute_id = zen_db_prepare_input((int)$data->attributes_id);

// edit
// attributes images
// when set to none remove from database
        if (isset($data->attributes_image) && zen_not_null($data->attributes_image) && ($data->attributes_image != 'none')) {
          $attributes_image = zen_db_prepare_input($data->attributes_image);
          $attributes_image_none = false;
        } else {
          $attributes_image = '';
          $attributes_image_none = true;
        }

        $attributes_image = new upload('attributes_image');
        $attributes_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
        $attributes_image->set_destination(DIR_FS_CATALOG_IMAGES . $data->img_dir);
        if ($attributes_image->parse() && $attributes_image->save($data->overwrite)) {
          $attributes_image_name = ($attributes_image->filename != 'none' ? ($data->img_dir . $attributes_image->filename) : '');
        } else {
          $attributes_image_name = ((isset($data->attributes_previous_image) && $data->attributes_image != 'none') ? $data->attributes_previous_image : '');
        }

        if ($data->image_delete == 1) {
          $attributes_image_name = '';
        }
        // turned off until working
        $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                      SET attributes_image = '" . zen_db_input($attributes_image_name) . "'
                      WHERE products_attributes_id = " . (int)$attribute_id);

        $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                      SET products_id = " . (int)$products_id . ",
                          options_id = " . (int)$options_id . ",
                          options_values_id = " . (int)$values_id . ",
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
          $products_attributes_filename = zen_db_prepare_input($data->products_attributes_filename);
          $products_attributes_maxdays = ($data->products_attributes_maxdays != '' ? (int)zen_db_prepare_input($data->products_attributes_maxdays) : DOWNLOAD_MAX_DAYS);
          $products_attributes_maxcount = ($data->products_attributes_maxcount != '' ? (int)zen_db_prepare_input($data->products_attributes_maxcount) : DOWNLOAD_MAX_COUNT);

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
    zen_update_products_price_sorter((int)$data->products_id);

    if (DOWNLOAD_ENABLED == 'true') {
      $attributeDownloadQuery = "SELECT products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                                 FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                                 WHERE products_attributes_id = " . (int)$data->attributes_id;
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
                                  WHERE products_id = " . $data->products_id . "
                                  LIMIT 1");

    // calculate current total attribute price
    $attributes_price_final = zen_get_attributes_price_final($data->attributes_id, 1, $data, 'false');
    $attributes_price_final_value = $attributes_price_final;
    $attributes_price_final = $currencies->display_price($attributes_price_final, zen_get_tax_rate($productCheck->fields['products_tax_class_id']), 1);
    $attributes_price_final_onetime = zen_get_attributes_price_final_onetime($data->attributes_id, 1, $data);
    $attributes_price_final_onetime = $currencies->display_price($attributes_price_final_onetime, zen_get_tax_rate($productCheck->fields['products_tax_class_id']), 1);
    $new_attributes_price = '';
    if ($data->attributes_discounted) {
      $new_attributes_price = zen_get_attributes_price_final($data->attributes_id, 1, '', 'false');
      $new_attributes_price = zen_get_discount_calc($data->products_id, true, $new_attributes_price);
      if ($new_attributes_price != $attributes_price_final_value) {
        $new_attributes_price = '|' . $currencies->display_price($new_attributes_price, zen_get_tax_rate($pInfo->products_tax_class_id), 1);
      } else {
        $new_attributes_price = '';
      }
    }

    $optionValuesRow['a'] = '&nbsp;';
    $optionValuesRow['b'] = '&nbsp;';
    $optionValuesRow['c'] = $data->attributes_id;
    $optionValuesRow['d'] = ((DOWNLOAD_ENABLED == 'true' && $products_attributes_filename != '') ? '<div class="smallText">' . $filename_is_missing . '&nbsp;' . $products_attributes_filename . '&nbsp;-&nbsp;' . TABLE_TEXT_MAX_DAYS_SHORT . '&nbsp;' . $products_attributes_maxdays . '&nbsp;-&nbsp;' . TABLE_TEXT_MAX_COUNT_SHORT . '&nbsp;' . $products_attributes_maxcount . '</div>' : '') . ($attributes_image_name != '' ? zen_image(DIR_WS_IMAGES . 'icon_status_yellow.gif') . '&nbsp;' : '&nbsp;&nbsp;') . zen_values_name($data->values_id);
    $optionValuesRow['e'] = $price_prefix . '&nbsp;' . $value_price;
    $optionValuesRow['f'] = $products_attributes_weight_prefix . '&nbsp;' . $products_attributes_weight;
    $optionValuesRow['g'] = $products_options_sort_order;
    $optionValuesRow['h']['attributes_display_only'] = '<button type="button" id="flag-' . (int)$attribute_id . '-attributes_display_only" class="btn btn-xs btn-default ' . ($attributes_display_only == '0' ? 'flagNotActive' : '') . '" onClick="switchFlag(\'' . ($attributes_display_only == '0' ? '1' : '0') . '\', \'' . (int)$attribute_id . '\', \'attributes_display_only\');" title="' . LEGEND_ATTRIBUTES_DISPLAY_ONLY . '"><i class="fa ' . ($attributes_display_only == '0' ? 'fa-times' : 'fa-check') . '" aria-hidden="true"></i></button>';
    $optionValuesRow['h']['product_attribute_is_free'] = '<button type="button" id="flag-' . (int)$attribute_id . '-product_attribute_is_free" class="btn btn-xs btn-default ' . ($product_attribute_is_free == '0' ? 'flagNotActive' : '') . '" onClick="switchFlag(\'' . ($product_attribute_is_free == '0' ? '1' : '0') . '\', \'' . (int)$attribute_id . '\', \'product_attribute_is_free\');" title="' . LEGEND_ATTRIBUTES_IS_FREE . '"><i class="fa ' . ($product_attribute_is_free == '0' ? 'fa-times' : 'fa-check') . '" aria-hidden="true"></i></button>';
    $optionValuesRow['h']['attributes_default'] = '<button type="button" id="flag-' . (int)$attribute_id . '-attributes_default" class="btn btn-xs btn-default ' . ($attributes_default == '0' ? 'flagNotActive' : '') . '" onClick="switchFlag(\'' . ($attributes_default == '0' ? '1' : '0') . '\', \'' . (int)$attribute_id . '\', \'attributes_default\');" title="' . LEGEND_ATTRIBUTES_DEFAULT . '"><i class="fa ' . ($attributes_default == '0' ? 'fa-times' : 'fa-check') . '" aria-hidden="true"></i></button>';
    $optionValuesRow['h']['attributes_discounted'] = '<button type="button" id="flag-' . (int)$attribute_id . '-attributes_discounted" class="btn btn-xs btn-default ' . ($attributes_discounted == '0' ? 'flagNotActive' : '') . '" onClick="switchFlag(\'' . ($attributes_discounted == '0' ? '1' : '0') . '\', \'' . (int)$attribute_id . '\', \'attributes_discounted\');" title="' . LEGEND_ATTRIBUTE_IS_DISCOUNTED . '"><i class="fa ' . ($attributes_discounted == '0' ? 'fa-times' : 'fa-check') . '" aria-hidden="true"></i></button>';
    $optionValuesRow['h']['attributes_price_base_included'] = '<button type="button" id="flag-' . (int)$attribute_id . '-attributes_price_base_included" class="btn btn-xs btn-default ' . ($attributes_price_base_included == '0' ? 'flagNotActive' : '') . '" onClick="switchFlag(\'' . ($attributes_price_base_included == '0' ? '1' : '0') . '\', \'' . (int)$attribute_id . '\', \'attributes_price_base_included\');" title="' . LEGEND_ATTRIBUTE_PRICE_BASE_INCLUDED . '"><i class="fa ' . ($attributes_price_base_included == '0' ? 'fa-times' : 'fa-check') . '" aria-hidden="true"></i></button>';
    $optionValuesRow['h']['attributes_required'] = '<button type="button" id="flag-' . (int)$attribute_id . '-attributes_required" class="btn btn-xs btn-default ' . ($attributes_required == '0' ? 'flagNotActive' : '') . '" onClick="switchFlag(\'' . ($attributes_required == '0' ? '1' : '0') . '\', \'' . (int)$attribute_id . '\', \'attributes_required\');" title="' . LEGEND_ATTRIBUTES_REQUIRED . '"><i class="fa ' . ($attributes_required == '0' ? 'fa-times' : 'fa-check') . '" aria-hidden="true></i></button>';
    $optionValuesRow['i'] = $attributes_price_final . $new_attributes_price . ' ' . $attributes_price_final_onetime;

    return([
      'optionValuesRow' => $optionValuesRow,
      'options_id' => $data->options_id,
      'values_id' => $data->values_id,
      'attribute_id' => $data->attributes_id
    ]);
  }

  public function deleteOptionConfirm() {
    $data = new objectInfo($_POST);

    $optionsName =  zen_options_name($data->options_id);
    $optionId = $data->options_id;
    return([
      'optionsName' => $optionsName,
      'optionId' => $optionId]);
  }

  public function deleteOption() {
    global $db, $messageStack;
    $data = new objectInfo($_POST);

    $getAttributesOptionsId = $db->Execute("SELECT products_attributes_id
                                            FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                                            WHERE products_id = " . (int)$data->products_id . "
                                            AND options_id = " . (int)$data->options_id);
    foreach ($getAttributesOptionsId as $attributesOptionsId) {
// remove any attached downloads
      $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                    WHERE products_attributes_id=  " . (int)$attributesOptionsId['products_attributes_id']);
// remove all option values
      $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                    WHERE products_id = " . (int)$data->products_id . "
                    AND options_id = " . (int)$data->options_id);
    }

    $messageStack->add_session(SUCCESS_ATTRIBUTES_DELETED_OPTION_NAME_VALUES . ' ID#' . zen_options_name((int)$data->options_id), 'success');

    // reset products_price_sorter for searches etc.
    zen_update_products_price_sorter((int)$data->options_id);

    return(['optionId' => $data->options_id]);
  }

  public function deleteOptionValueConfirm() {
    global $db;
    $data = new objectInfo($_POST);

    $attribute = $db->Execute("SELECT options_id,options_values_id
                               FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                               WHERE products_attributes_id = " . (int)$data->attributes_id);

    $deleteOptionValueName = zen_options_name($attribute->fields['options_id']) . ' => ' . zen_values_name($attribute->fields['options_values_id']);
    $attributesId = $data->attributes_id;

    return([
      'deleteOptionValueName' => $deleteOptionValueName,
      'attributesId' => $attributesId]);
  }

  public function deleteOptionValue() {
    global $db, $zco_notifier;
    $data = new objectInfo($_POST);

    $attribute_id = zen_db_prepare_input((int)$data->attributes_id);

    $zco_notifier->notify('NOTIFY_ATTRIBUTE_CONTROLLER_DELETE_ATTRIBUTE', array('attribute_id' => $attribute_id), $attribute_id);

    $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
                  WHERE products_attributes_id = " . (int)$attribute_id);

    $db->Execute("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                  WHERE products_attributes_id = " . (int)$attribute_id);

    // reset products_price_sorter for searches etc.
    zen_update_products_price_sorter($data->products_id);
    $attributesId = $data->attributes_id;
    return (['attributesId' => $attributesId]);
  }

  public function switchFlag() {
    global $db;
    $data = new objectInfo($_POST);
    if ($data->flag == '1') {
      $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                    SET " . $data->flag_name . " = 1
                    WHERE products_id = " . (int)$data->products_id . "
                    AND products_attributes_id = " . (int)$data->attributes_id);

      $flagValue = 0;
    } else {
      $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . "
                    SET " . $data->flag_name . " = 0
                    WHERE products_id = " . (int)$data->products_id . "
                    AND products_attributes_id = " . (int)$data->attributes_id);

      $flagValue = 1;
    }
    return(['flagValue' => $flagValue]);
  }

  public function messageStack() {
    global $messageStack;
    if ($messageStack->size > 0) {
      return(['modalMessageStack' => $messageStack->output()]);
    }
  }

}
