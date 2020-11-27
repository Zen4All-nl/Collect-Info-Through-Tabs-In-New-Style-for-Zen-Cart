<?php

class zcAjaxAdminCategories extends base {

  public function add_type()
  {
    global $db;
    $data = new objectInfo($_POST);
    // check if it is already restricted
    $sql = "SELECT *
            FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . "
            WHERE category_id = " . (int)zen_db_prepare_input($data->categoryId) . "
            AND product_type_id = " . (int)zen_db_prepare_input($data->restrictType);

    $type_to_cat = $db->Execute($sql);
    if ($type_to_cat->RecordCount() < 1) {
      //@@TODO find all sub-categories and restrict them as well.

      $insert_sql_data = [
        'category_id' => zen_db_prepare_input($data->categoryId),
        'product_type_id' => zen_db_prepare_input($data->restrictType)];

      zen_db_perform(TABLE_PRODUCT_TYPES_TO_CATEGORY, $insert_sql_data);
    }
    // add product type restrictions to subcategories if not already set
    if ($data->add_type_all == 'true') {
      zen_restrict_sub_categories($data->categoryId, $data->restrictType);
    }

    $restrictTypesQuery = "SELECT *
                           FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . "
                           WHERE category_id = " . (int)$data->categoryId;

    $restrictTypes = $db->Execute($restrictTypesQuery);
    if ($restrictTypes->RecordCount() > 0) {
      $returnData['restrictTypes'] = [];
      foreach ($restrictTypes as $restrictType) {
        $typeQuery = "SELECT type_name
                      FROM " . TABLE_PRODUCT_TYPES . "
                      WHERE type_id = " . (int)$restrictType['product_type_id'];
        $type = $db->Execute($typeQuery);
        $returnData['restrictTypes'][] = [
          'type_name' => $type->fields['type_name'],
          'type_id' => $restrictType['product_type_id']];
      }
    }
    return (['restrictTypes' => $returnData['restrictTypes']]);
  }

  public function remove_type()
  {
    global $db;

    $data = new objectInfo($_POST);
    $sql = "DELETE FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . "
            WHERE category_id = " . (int)zen_db_prepare_input($data->categoryId) . "
            AND product_type_id = " . (int)zen_db_prepare_input($data->restrictType);

    $db->Execute($sql);
    zen_remove_restrict_sub_categories($data->categoryId, (int)$data->restrictType);

    $restrictTypesQuery = "SELECT *
                           FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . "
                           WHERE category_id = " . (int)$data->categoryId;

    $restrictTypes = $db->Execute($restrictTypesQuery);
    if ($restrictTypes->RecordCount() > 0) {
      $returnData['restrictTypes'] = [];
      foreach ($restrictTypes as $restrictType) {
        $typeQuery = "SELECT type_name
                      FROM " . TABLE_PRODUCT_TYPES . "
                      WHERE type_id = " . (int)$restrictType['product_type_id'];
        $type = $db->Execute($typeQuery);
        $returnData['restrictTypes'][] = [
          'type_name' => $type->fields['type_name'],
          'type_id' => $restrictType['product_type_id']];
      }
    }
    return (['restrictTypes' => $returnData['restrictTypes']]);
  }

  public function save_category()
  {
    global $db, $messageStack;
    $languages = zen_get_languages();
    $data = new objectInfo($_POST);

    if (isset($data->categories_id)) {
      $categories_id = zen_db_prepare_input($data->categories_id);
    }
    $sort_order = zen_db_prepare_input($data->sort_order);

    $sql_data_array = ['sort_order' => (int)$sort_order];

    if ($data->action == 'insert_category') {
      $insert_sql_data = [
        'parent_id' => (int)$data->parent_category_id,
        'date_added' => 'now()'];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      zen_db_perform(TABLE_CATEGORIES, $sql_data_array);

      $categories_id = zen_db_insert_id();
      // check if [arent is restricted
      $parentCatQuery = "SELECT parent_id
              FROM " . TABLE_CATEGORIES . "
              WHERE categories_id = " . (int)$categories_id;

      $parentCat = $db->Execute($parentCatQuery);
      if ($parentCat->fields['parent_id'] != '0') {
        $hasTypequery = "SELECT *
                FROM " . TABLE_PRODUCT_TYPES_TO_CATEGORY . "
                WHERE category_id = '" . $parentCat->fields['parent_id'] . "'";
        $hasType = $db->Execute($hasTypequery);
        if ($hasType->RecordCount() > 0) {
          foreach ($hasType as $item) {
            $insert_sql_data = [
              'category_id' => (int)$categories_id,
              'product_type_id' => (int)$item['product_type_id']];
            zen_db_perform(TABLE_PRODUCT_TYPES_TO_CATEGORY, $insert_sql_data);
          }
        }
      }
    } elseif ($data->action == 'update_category') {
      $update_sql_data = ['last_modified' => 'now()'];

      $sql_data_array = array_merge($sql_data_array, $update_sql_data);

      zen_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = " . (int)$categories_id);
    }

    $categories_name_array = $data->categories_name;
    $categories_description_array = $data->categories_description;
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = (int)$languages[$i]['id'];

      // clean $categories_description when blank or just <p /> left behind
      $sql_data_array = [
        'categories_name' => zen_db_prepare_input($categories_name_array[$language_id]),
        'categories_description' => ($categories_description_array[$language_id] == '<p />' ? '' : zen_db_prepare_input($categories_description_array[$language_id]))];
      if ($data->action == 'insert_category') {
        $insert_sql_data = [
          'categories_id' => (int)$categories_id,
          'language_id' => (int)$language_id];
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        zen_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
        $messageStack->add_session(SUCCESS_CATEGORY_INSERTED . $categories_id . ' ' . $data->categories_name[$_SESSION['languages_id']], 'success');
      } elseif ($data->action == 'update_category') {
        zen_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "categories_id = " . (int)$categories_id . " and language_id = " . (int)$language_id);
        $messageStack->add_session(SUCCESS_CATEGORY_UPDATED . $categories_id . ' ' . $data->categories_name[$_SESSION['languages_id']], 'success');
      }
    }

    if ($data->categories_image_manual != '') {
      // add image manually
      $categories_image_name = zen_db_input($data->img_dir . $data->categories_image_manual);
      $db->Execute("UPDATE " . TABLE_CATEGORIES . "
                    SET categories_image = '" . $categories_image_name . "'
                    WHERE categories_id = " . (int)$categories_id);
    } else {
      if ($categories_image = new upload('categories_image')) {
        $categories_image->set_extensions(array('jpg', 'jpeg', 'gif', 'png', 'webp', 'flv', 'webm', 'ogg'));
        $categories_image->set_destination(DIR_FS_CATALOG_IMAGES . $data->img_dir);
        if ($categories_image->parse() && $categories_image->save()) {
          $categories_image_name = zen_db_input($data->img_dir . $categories_image->filename);
        }
        if ($categories_image->filename != 'none' && $categories_image->filename != '' && $data->image_delete != 1) {
          // save filename when not set to none and not blank
          $db->Execute("UPDATE " . TABLE_CATEGORIES . "
                        SET categories_image = '" . $categories_image_name . "'
                        WHERE categories_id = " . (int)$categories_id);
        } else {
          // remove filename when set to none and not blank
          if ($categories_image->filename != '' || $data->image_delete == 1) {
            $db->Execute("UPDATE " . TABLE_CATEGORIES . "
                          SET categories_image = ''
                          WHERE categories_id = " . (int)$categories_id);
          }
        }
      }
    }
    // add or update meta tags
    $action = '';
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $language_id = (int)$languages[$i]['id'];
      $check = $db->Execute("SELECT *
                             FROM " . TABLE_METATAGS_CATEGORIES_DESCRIPTION . "
                             WHERE categories_id = " . (int)$categories_id . "
                             AND language_id = " . $language_id);
      if ($check->RecordCount() > 0) {
        $action = 'update_category_meta_tags';
      } else {
        $action = 'insert_categories_meta_tags';
      }
      if (empty($data->metatags_title[$language_id]) && empty($data->metatags_keywords[$language_id]) && empty($data->metatags_description[$language_id])) {
        $action = 'delete_category_meta_tags';
      }

      $sql_data_array = array(
        'metatags_title' => zen_db_prepare_input($data->metatags_title[$language_id]),
        'metatags_keywords' => zen_db_prepare_input($data->metatags_keywords[$language_id]),
        'metatags_description' => zen_db_prepare_input($data->metatags_description[$language_id]));

      if ($action == 'insert_categories_meta_tags') {
        $insert_sql_data = array(
          'categories_id' => (int)$categories_id,
          'language_id' => (int)$language_id);
        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        zen_db_perform(TABLE_METATAGS_CATEGORIES_DESCRIPTION, $sql_data_array);
      } elseif ($action == 'update_category_meta_tags') {
        zen_db_perform(TABLE_METATAGS_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "categories_id = " . (int)$categories_id . " and language_id = " . (int)$language_id);
      } elseif ($action == 'delete_category_meta_tags') {
        $remove_categories_metatag = "DELETE FROM " . TABLE_METATAGS_CATEGORIES_DESCRIPTION . "
                                      WHERE categories_id = " . (int)$categories_id . "
                                      AND language_id = " . (int)$language_id;
        $db->Execute($remove_categories_metatag);
      }
    }
    return (['categoryId' => (int)$categories_id]);
  }

}
