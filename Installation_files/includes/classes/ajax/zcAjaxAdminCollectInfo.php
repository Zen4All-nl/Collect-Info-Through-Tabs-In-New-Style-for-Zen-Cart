<?php

class zcAjaxAdminCollectInfo extends base {

  private function setProductId($productId, $current_category_id)
  {
    global $db, $messageStack;
    $languages = zen_get_languages();
    if (isset($productId) && $productId != '') {
      return $productId;
    } else {
      $sql_data_array['products_date_added'] = 'now()';
      $sql_data_array['master_categories_id'] = (int)$current_category_id;

      zen_db_perform(TABLE_PRODUCTS, $sql_data_array);
      $productId = zen_db_insert_id();

// reset products_price_sorter for searches etc.
      zen_update_products_price_sorter($productId);

      $db->Execute("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                    VALUE (" . (int)$productId . ", " . (int)$current_category_id . ")");

      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];

        $sql_data_array = array(
          'products_name' => '',
          'products_description' => '',
          'products_url' => '');

        $insert_sql_data = array(
          'products_id' => (int)$productId,
          'language_id' => (int)$language_id);

        $sql_array = array_merge($sql_data_array, $insert_sql_data);
        zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_array);
      }
      zen_record_admin_activity('New product ' . (int)$productId . ' added via admin console.', 'info');
      $messageStack->add_session('New product ' . (int)$productId, 'success');
      return $productId;
    }
  }

  public function setImage()
  {
    $data = new objectInfo($_POST);

    $productId = $this->setProductId($data->productId, $data->current_category_id);

    $products_image_name = $newImage['products_image']['name'];
    /*   if ($data->products_image_manual'] != '') {
      $products_image_name_manual = $data->img_dir . $data->products_image_manual;
      $products_image_name = $products_image_name_manual;
      }
      $new_products_image_name = $products_image_name; */
    $products_image = new upload('products_image');
    $products_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
    $products_image->set_destination(DIR_FS_CATALOG_IMAGES . $data->img_dir);
    if ($products_image->parse() && $products_image->save($data->overwrite == '1')) {
      $products_image_name = $data->img_dir . $products_image->filename;
    } else {
      $products_image_name = (isset($data->products_previous_image) ? $data->products_previous_image : '');
    }
    $sql_data_array['products_image'] = zen_db_prepare_input($data->products_image);
    $new_image = 'true';

    if ($data->image_delete == 1) {
      $sql_data_array['products_image'] = '';
      $new_image = 'false';
    }
    zen_db_perform(TABLE_PRODUCTS, $sql_data_array);
    return([
      'data' => $data,
      'products_image_name' => $products_image_name,
      'image_dir' => $data->img_dir,
      'productId' => $productId]);
  }

  public function saveProduct()
  {
    global $db, $messageStack;
    $data = new objectInfo($_POST);
    $languages = zen_get_languages();
    $productId = $this->setProductId($data->productId, $data->current_category_id);

    if ($data->products_model . $data->products_url . $data->products_name . $data->products_description != '') {
      $products_date_available = zen_db_prepare_input($data->products_date_available);
      $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';
      $master_categories_id = (!empty($data->master_category) && (int)$data->master_category > 0 ? (int)$data->master_category : (int)$data->master_categories_id);
// Data-cleaning to prevent data-type mismatch errors:
      $tables = '';
      $sql_data_fields = "products_type = " . (int)$data->product_type . ", products_date_available = '" . $products_date_available . "', products_last_modified = now(), master_categories_id = " . $master_categories_id;
      foreach ($data as $key => $field) {
        if (!is_array($field)) {
          $sql_data_array[$key] = $field;
        }
      }
      $db->Execute("UPDATE " . TABLE_PRODUCTS . " p
                    INNER JOIN " . TABLE_PRODUCT_MUSIC_EXTRA . " pme ON (p.products_id = pme.products_id)
                    " . $tables . "
                    SET " . $sql_data_fields . "
                        WHERE p.userid = 1 AND a.lid = 1 AND b.userid = 1");
      /*   $sql_data_array = array(
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
        ); */
// when set to none remove from database
// is out dated for browsers use radio only

      $sql_data_array['products_last_modified'] = 'now()';
      $sql_data_array['master_categories_id'] = (!empty($data->master_category) && (int)$data->master_category > 0 ? (int)$data->master_category : (int)$data->master_categories_id);

      /* zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = " . (int)$productId);

      zen_record_admin_activity('Updated product ' . (int)$productId . ' via admin console.', 'info');
      $messageStack->add_session('Updated product ' . (int)$productId, 'success');
// reset products_price_sorter for searches etc.
      zen_update_products_price_sorter((int)$productId);

///////////////////////////////////////////////////////
//// INSERT PRODUCT-TYPE-SPECIFIC *UPDATES* HERE //////
////    *END OF PRODUCT-TYPE-SPECIFIC UPDATES* ////////
///////////////////////////////////////////////////////

      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];

        $sql_data_array = array(
          'products_name' => zen_db_prepare_input($data->products_name[$language_id]),
          'products_description' => zen_db_prepare_input($data->products_description[$language_id]),
          'products_url' => zen_db_prepare_input($data->products_url[$language_id]));

        zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = " . (int)$productId . " and language_id = " . (int)$language_id);
      }

// add meta tags

      $meta_status_array = array(
        'metatags_title_status' => (int)$data->metatags_title_status,
        'metatags_products_name_status' => (int)$data->metatags_products_name_status,
        'metatags_model_status' => (int)$data->metatags_model_status,
        'metatags_price_status' => (int)$data->metatags_price_status,
        'metatags_title_tagline_status' => (int)$data->metatags_title_tagline_status
      );

      $update_sql_data = array('products_last_modified' => 'now()');

      $meta_update_data_array = array_merge($meta_status_array, $update_sql_data);
//die('UPDATE PRODUCTS ID:' . (int)$products_id . ' - ' . sizeof($sql_data_array));
      zen_db_perform(TABLE_PRODUCTS, $meta_update_data_array, 'update', "products_id = " . (int)$productId);

      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];

        $sql_data_array = array(
          'metatags_title' => zen_db_prepare_input($data->metatags_title[$language_id]),
          'metatags_keywords' => zen_db_prepare_input($data->metatags_keywords[$language_id]),
          'metatags_description' => zen_db_prepare_input($data->metatags_description[$language_id]));

        if ($n == 1 && empty($data->metatags_title[$language_id]) && empty($data->metatags_keywords[$language_id]) && empty($data->metatags_description[$language_id])) {
          $remove_products_metatag = "DELETE FROM " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . "
                                      WHERE products_id = " . (int)$productId . "
                                      AND language_id = " . (int)$language_id;
          $db->Execute($remove_products_metatag);
        } else {

        zen_db_perform(TABLE_META_TAGS_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = " . (int)$productId . " and language_id = " . (int)$language_id);
        }
        }

        $extraTabsupate = dirList(DIR_WS_MODULES . 'extra_tabs/', 'tab_update_product.php');
        if (isset($extraTabsupate) && $extraTabsupate != '') {
        foreach ($extraTabsupate as $tabUpdate) {
        include($tabUpdate);
        }
        } */
    } else {
      $messageStack->add_session(ERROR_NO_DATA_TO_SAVE, 'error');
    }
    return(['test' => $test]);
  }

  public function messageStack()
  {
    global $messageStack;
    if ($messageStack->size > 0) {
      return([
        'modalMessageStack' => $messageStack->output()]);
    }
  }

}

    /**
     * NOTE: THIS IS HERE FOR BACKWARD COMPATIBILITY. The function is properly declared in the functions files instead.
     * Convert value to a float -- mainly used for sanitizing and returning non-empty strings or nulls
     * @param int|float|string $input
     * @return float|int
     */
    if (!function_exists('convertToFloat')) {

      function convertToFloat($input = 0)
      {
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