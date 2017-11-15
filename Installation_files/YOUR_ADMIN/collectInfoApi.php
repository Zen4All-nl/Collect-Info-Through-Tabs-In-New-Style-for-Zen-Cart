<?php

require('includes/application_top.php');

$returnData = array();
$data = new objectInfo($_POST);
$newImage = (isset($_FILES) ? $_FILES : '');
$returnData['newImage'] = $newImage;
// $returnData['dataToApi'] is used for debugging, to see which data is send to api
$returnData['dataToApi'] = $data;
switch ($data->view) {
  case 'setImage' : {
      $products_image_name = $newImage['products_image']['name'];
      /*   if ($data->products_image_manual'] != '') {
        $products_image_name_manual = $data->img_dir . $data->products_image_manual;
        $products_image_name = $products_image_name_manual;
        }
        $new_products_image_name = $products_image_name; */
      $products_image = new upload($products_image_name);
      $returnData['this'] = $products_image;
      $products_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
      $products_image->set_destination(DIR_FS_CATALOG_IMAGES . $data->img_dir);
      $returnData['image3'] = $products_image;
      if ($products_image->parse() && $products_image->save($data->overwrite == '1')) {
        $products_image_name = $data->img_dir . $products_image->filename;
        $returnData['image'] = '1';
      } else {
        $products_image_name = (isset($data->products_previous_image) ? $data->products_previous_image : '');
        $returnData['image'] = '2';
      }
      // $returnData['products_image'] = $products_image;
      $returnData['products_image_name'] = $products_image_name;
      break;
    }
  case 'setImage1' : {
      move_uploaded_file($_FILES['products_image']['tmp_name'], DIR_FS_CATALOG_IMAGES . $data->img_dir . $_FILES['products_image']['name']);
    }
  case 'saveProduct' : {

      $languages = zen_get_languages();
      $action = '';
      if (isset($data->productId) && $data->productId != '') {
        $products_id = zen_db_prepare_input($data->productId);
        $action = 'update_product';
      } else {
        $action = 'insert_product';
      }
      if ($data->products_model . $data->products_url . $data->products_name . $data->products_description != '') {
        $products_date_available = zen_db_prepare_input($data->products_date_available);
        $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';
        // Data-cleaning to prevent data-type mismatch errors:
        $sql_data_array = array(
          'products_quantity' => convertToFloat($data->products_quantity),
          'products_type' => (int)$data->product_type,
          'products_model' => zen_db_prepare_input($data->products_model),
          'products_price' => convertToFloat($data->products_price),
          'products_date_available' => $products_date_available,
          'products_weight' => convertToFloat($data->products_weight),
          'products_status' => (int)$data->products_status,
          'products_virtual' => (int)$data->products_virtual,
          'products_tax_class_id' => (int)$data->products_tax_class_id,
          'manufacturers_id' => (int)$data->manufacturers_id,
          'products_quantity_order_min' => convertToFloat($data->products_quantity_order_min) == 0 ? 1 : convertToFloat($data->products_quantity_order_min),
          'products_quantity_order_units' => convertToFloat($data->products_quantity_order_units) == 0 ? 1 : convertToFloat($data->products_quantity_order_units),
          'products_priced_by_attribute' => (int)$data->products_priced_by_attribute,
          'product_is_free' => (int)$data->product_is_free,
          'product_is_call' => (int)$data->product_is_call,
          'products_quantity_mixed' => (int)$data->products_quantity_mixed,
          'product_is_always_free_shipping' => (int)$data->product_is_always_free_shipping,
          'products_qty_box_status' => (int)$data->products_qty_box_status,
          'products_quantity_order_max' => convertToFloat($data->products_quantity_order_max),
          'products_sort_order' => (int)$data->products_sort_order,
          'products_discount_type' => (int)$data->products_discount_type,
          'products_discount_type_from' => (int)$data->products_discount_type_from,
          'products_price_sorter' => convertToFloat($data->products_price_sorter),
        );
$returnData['sql_data_array'] = $sql_data_array;
        // when set to none remove from database
        // is out dated for browsers use radio only
        /*  $sql_data_array['products_image'] = zen_db_prepare_input($data->products_image);
          $new_image = 'true';

          if ($data->image_delete == 1) {
          $sql_data_array['products_image'] = '';
          $new_image = 'false';
          }
         */
        if ($action == 'insert_product') {
          $sql_data_array['products_date_added'] = 'now()';
          $sql_data_array['master_categories_id'] = (int)$current_category_id;

          zen_db_perform(TABLE_PRODUCTS, $sql_data_array);
          $products_id = zen_db_insert_id();

          // reset products_price_sorter for searches etc.
          zen_update_products_price_sorter($products_id);

          $db->Execute("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                        VALUE ('" . (int)$products_id . "', '" . (int)$current_category_id . "')");

          zen_record_admin_activity('New product ' . (int)$products_id . ' added via admin console.', 'info');

          ///////////////////////////////////////////////////////
          //// INSERT PRODUCT-TYPE-SPECIFIC *INSERTS* HERE //////
          ////    *END OF PRODUCT-TYPE-SPECIFIC INSERTS* ////////
          ///////////////////////////////////////////////////////
        } elseif ($action == 'update_product') {
          $sql_data_array['products_date_added'] = 'now()';
          $sql_data_array['master_categories_id'] = ((int)$data->master_category > 0 ? (int)$data->master_category : (int)$data->master_categories_id);

          zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = " . (int)$products_id);

          zen_record_admin_activity('Updated product ' . (int)$products_id . ' via admin console.', 'info');

          // reset products_price_sorter for searches etc.
          zen_update_products_price_sorter((int)$products_id);

          ///////////////////////////////////////////////////////
          //// INSERT PRODUCT-TYPE-SPECIFIC *UPDATES* HERE //////
          ////    *END OF PRODUCT-TYPE-SPECIFIC UPDATES* ////////
          ///////////////////////////////////////////////////////
        }

        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];

          $sql_data_array = array(
            'products_name' => zen_db_prepare_input($data->products_name[$language_id]),
            'products_description' => zen_db_prepare_input($data->products_description[$language_id]),
            'products_url' => zen_db_prepare_input($data->products_url[$language_id]));

          if ($action == 'insert_product') {
            $insert_sql_data = array(
              'products_id' => (int)$products_id,
              'language_id' => (int)$language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
          } elseif ($action == 'update_product') {
            zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = " . (int)$products_id . " and language_id = " . (int)$language_id);
          }
        }

        // add meta tags

        $sql_data_array = array(
          'metatags_title_status' => (int)$data->metatags_title_status,
          'metatags_products_name_status' => (int)$data->metatags_products_name_status,
          'metatags_model_status' => (int)$data->metatags_model_status,
          'metatags_price_status' => (int)$data->metatags_price_status,
          'metatags_title_tagline_status' => (int)$data->metatags_title_tagline_status
        );

        if ($action == 'insert_product') {
          zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
        } elseif ($action == 'update_product') {
          $update_sql_data = array('products_last_modified' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $update_sql_data);
//die('UPDATE PRODUCTS ID:' . (int)$products_id . ' - ' . sizeof($sql_data_array));
          zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "'");
        }

// check if new meta tags or existing
        $check_meta_tags_description = $db->Execute("SELECT products_id
                                                     FROM " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . "
                                                     WHERE products_id = " . (int)$products_id);
        if ($check_meta_tags_description->RecordCount() <= 0) {
          $action = 'insert_product';
        }

        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];

          $sql_data_array = array(
            'metatags_title' => zen_db_prepare_input($data->metatags_title[$language_id]),
            'metatags_keywords' => zen_db_prepare_input($data->metatags_keywords[$language_id]),
            'metatags_description' => zen_db_prepare_input($data->metatags_description[$language_id]));

          if ($action == 'insert_product') {
            $insert_sql_data = array(
              'products_id' => (int)$products_id,
              'language_id' => (int)$language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            zen_db_perform(TABLE_META_TAGS_PRODUCTS_DESCRIPTION, $sql_data_array);
            $returnData['productId'] = (int)$products_id;
          } elseif ($action == 'update_product') {
            if ($n == 1 && empty($data->metatags_title[$language_id]) && empty($data->metatags_keywords[$language_id]) && empty($data->metatags_description[$language_id])) {
              $remove_products_metatag = "DELETE FROM " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . "
                                          WHERE products_id = " . (int)$products_id . "
                                          AND language_id = " . (int)$language_id;
              $db->Execute($remove_products_metatag);
            } else {

              zen_db_perform(TABLE_META_TAGS_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and language_id = '" . (int)$language_id . "'");
            }
          }
        }

        $extraTabsupate = dirList(DIR_WS_MODULES . 'extra_tabs/', 'tab_update_product.php');
        if (isset($extraTabsupate) && $extraTabsupate != '') {
          foreach ($extraTabsupate as $tabUpdate) {
            include($tabUpdate);
          }
        }
        $messageStack->add_session(PRODUCT_DATA_SAVED, 'succes');
      } else {
        $messageStack->add_session(ERROR_NO_DATA_TO_SAVE, 'error');
      }

      /**
       * NOTE: THIS IS HERE FOR BACKWARD COMPATIBILITY. The function is properly declared in the functions files instead.
       * Convert value to a float -- mainly used for sanitizing and returning non-empty strings or nulls
       * @param int|float|string $input
       * @return float|int
       */
      if (!function_exists('convertToFloat')) {

        function convertToFloat($input = 0) {
          if ($input === null) {
            return 0;
          }
          $val = preg_replace('/[^0-9,\.\-]/', '', $input);
          // do a non-strict compare here:
          if ($val == 0) {
            return 0;
          }

          return (float)$val;
        }

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
