<?php

/**
 * 
 */
class zcAjaxAdminProduct extends base {

  /**
   * 
   * @global type $db
   * @global array $messageStack
   * @param integer $productId
   * @param integer $current_category_id
   * @return integer
   */
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

  /**
   * 
   * @global type $db
   * @return array
   */
  public function setImage()
  {
    global $db;
    $data = new objectInfo($_POST);

    $productId = $this->setProductId($data->productId, $data->current_category_id);

    //    $new_products_image_name = $products_image_name;
    $products_image = new upload('products_image');
    $products_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
    $products_image->set_destination(DIR_FS_CATALOG_IMAGES . $data->img_dir);
    if ($products_image->parse() && $products_image->save($data->overwrite == '1')) {
      $products_image_name = $data->img_dir . $products_image->filename;
    } else {
      $products_image_name = (isset($data->products_previous_image) ? $data->products_previous_image : '');
    }

    if ($data->products_image_manual != '') {
      $products_image_name_manual = $data->img_dir . $data->products_image_manual;
      $products_image_name = $products_image_name_manual;
    }

    $new_image = 'true';

    if ($data->image_delete == 1) {
      $products_image_name = '';
      $new_image = 'false';
    }

    $db_filename = zen_limit_image_filename($products_image_name, TABLE_PRODUCTS, 'products_image');
    $db->Execute("UPDATE " . TABLE_PRODUCTS . "
                  SET products_image = '" . $db_filename . "'
                  WHERE products_id = " . $productId);
    return([
      'data' => $data,
      'products_image_name' => $products_image_name,
      'image_dir' => $data->img_dir,
      'productId' => $productId]);
  }

  /**
   * 
   * @global type $db
   */
  public function deleteMainImage()
  {
    global $db;
    $data = new objectInfo($_POST);
    $db->Execute("UPDATE " . TABLE_PRODUCTS . "
                  SET products_image = ''
                  WHERE products_id = " . $data->productId);
  }

  /**
   * 
   * @global type $db
   * @global array $messageStack
   * @return type
   */
  public function saveProduct()
  {
    global $db, $messageStack;
    $data = new objectInfo($_POST);
    $excludeArray = [
      'securityToken',
      'productId',
      'current_category_id',
      'cPath',
      'products_date_available',
      'products_price_gross',
      'products_previous_image',
      'products_last_modified',
      'master_categories_id'
    ];
    $languages = zen_get_languages();
    $productId = $this->setProductId($data->productId, $data->current_category_id);

    foreach ($data as $key => $field) {
      if (!in_array($key, $excludeArray) && !is_array($field)) {
        $sql_data_array[$key] = $field;
      }
    }

    // Data-cleaning to prevent data-type mismatch errors:
    $sql_data_array['products_quantity'] = convertToFloat($sql_data_array['products_quantity']);
    $sql_data_array['products_type'] = (int)$sql_data_array['products_type'];
    $sql_data_array['products_model'] = zen_db_prepare_input($sql_data_array['products_model']);
    $sql_data_array['products_price'] = convertToFloat($sql_data_array['products_price']);
    $sql_data_array['products_weight'] = convertToFloat($sql_data_array['products_weight']);
    $sql_data_array['products_status'] = (int)$sql_data_array['products_status'];
    $sql_data_array['products_virtual'] = (int)$sql_data_array['products_virtual'];
    $sql_data_array['products_tax_class_id'] = (int)$sql_data_array['products_tax_class_id'];
    $sql_data_array['manufacturers_id'] = (int)$sql_data_array['manufacturers_id'];
    $sql_data_array['products_quantity_order_min'] = convertToFloat($sql_data_array['products_quantity_order_min']) == 0 ? 1 : convertToFloat($sql_data_array['products_quantity_order_min']);
    $sql_data_array['products_quantity_order_units'] = convertToFloat($sql_data_array['products_quantity_order_units']) == 0 ? 1 : convertToFloat($sql_data_array['products_quantity_order_units']);
    $sql_data_array['products_priced_by_attribute'] = (int)$sql_data_array['products_priced_by_attribute'];
    $sql_data_array['product_is_free'] = (int)$sql_data_array['product_is_free'];
    $sql_data_array['product_is_call'] = (int)$sql_data_array['product_is_call'];
    $sql_data_array['products_quantity_mixed'] = (int)$sql_data_array['products_quantity_mixed'];
    $sql_data_array['product_is_always_free_shipping'] = (int)$sql_data_array['product_is_always_free_shipping'];
    $sql_data_array['products_qty_box_status'] = (int)$sql_data_array['products_qty_box_status'];
    $sql_data_array['products_quantity_order_max'] = convertToFloat($sql_data_array['products_quantity_order_max']);
    $sql_data_array['products_sort_order'] = (int)$sql_data_array['products_sort_order'];
    $sql_data_array['products_discount_type'] = (int)$sql_data_array['products_discount_type'];
    $sql_data_array['products_discount_type_from'] = (int)$sql_data_array['products_discount_type_from'];
    $sql_data_array['products_price_sorter'] = convertToFloat($sql_data_array['products_price_sorter']);

    $products_date_available = zen_db_prepare_input($data->products_date_available);
    $sql_data_array['products_date_available'] = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

    $sql_data_array['products_last_modified'] = 'now()';
    $sql_data_array['master_categories_id'] = (!empty($data->master_category) && (int)$data->master_category > 0 ? (int)$data->master_category : (int)$data->master_categories_id);

    zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = " . (int)$productId);

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

      $sql_lang_data_array = array(
        'products_name' => zen_db_prepare_input($data->products_name[$language_id]),
        'products_description' => zen_db_prepare_input($data->products_description[$language_id]),
        'products_url' => zen_db_prepare_input($data->products_url[$language_id]));

      zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_lang_data_array, 'update', "products_id = " . (int)$productId . " AND language_id = " . (int)$language_id);
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

    zen_db_perform(TABLE_PRODUCTS, $meta_update_data_array, 'update', 'products_id = ' . (int)$productId);

    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $language_id = $languages[$i]['id'];

      $sql_data_array = array(
        'metatags_title' => zen_db_prepare_input($data->metatags_title[$language_id]),
        'metatags_keywords' => zen_db_prepare_input($data->metatags_keywords[$language_id]),
        'metatags_description' => zen_db_prepare_input($data->metatags_description[$language_id])
      );

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
    }

    return;
  }

}
