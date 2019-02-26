<?php

/**
 * 
 */
class zcAjaxAdminProductLayoutEditor extends base {

  /**
   * Add product type
   * @global string $db
   * @return array
   */
  public function addProducType()
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
      $restrictTypes = [];
      foreach ($restrictTypes as $restrictType) {
        $typeQuery = "SELECT type_name
                      FROM " . TABLE_PRODUCT_TYPES . "
                      WHERE type_id = " . (int)$restrictType['product_type_id'];
        $type = $db->Execute($typeQuery);
        $restrictTypes[] = [
          'type_name' => $type->fields['type_name'],
          'type_id' => $restrictType['product_type_id']];
      }
    }
    return(['restrictTypes' => $restrictTypes]);
  }

  /**
   * Remove product type
   * @global string $db
   * @return array
   */
  public function removeProductType()
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
      $restrictTypes = [];
      foreach ($restrictTypes as $restrictType) {
        $typeQuery = "SELECT type_name
                      FROM " . TABLE_PRODUCT_TYPES . "
                      WHERE type_id = " . (int)$restrictType['product_type_id'];
        $type = $db->Execute($typeQuery);
        $restrictTypes[] = [
          'type_name' => $type->fields['type_name'],
          'type_id' => $restrictType['product_type_id']];
      }
    }
    return(['restrictTypes' => $restrictTypes]);
  }

  /**
   * Get the information of a tab, why it can not be deleted
   * @global string $db
   * @return array
   */
  public function getTabInfo()
  {
    global $db;
    $data = new objectInfo($_POST);
    $TabInfoQuery = "SELECT *
                     FROM " . TABLE_PRODUCT_TABS . "
                     WHERE id = " . (int)$data->tabId;
    $TabInfo = $db->Execute($TabInfoQuery);
    $tabIsCore = ($TabInfo->fields['core'] == '1' ? '1' : '0');
    $productTypeIds = ($TabInfo->fields['product_type_id'] != '' ? explode('|', $TabInfo->fields['product_type_id']) : []);
    $productTypeNames = '';
    if (!empty($productTypeIds)) {
      foreach ($productTypeIds as $productTypeId) {
        $productTypeNameQuery = "SELECT type_name
                                 FROM " . TABLE_PRODUCT_TYPES . "
                                 WHERE type_id = " . $productTypeId;
        $productTypeName = $db->Execute($productTypeNameQuery);
        $productTypeNames .= '<li>' . $productTypeName->fields['type_name'] . '</li>' . PHP_EOL;
      }
    } else {
      $productTypeNames .= '<li>' . TEXT_NONE . '</li>';
    }
    $UsedProductFieldsQuery = "SELECT DISTINCT field_name
                               FROM " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
                               WHERE tab_id = " . (int)$data->tabId;
    $UsedProductFields = $db->Execute($UsedProductFieldsQuery);
    $UsedProductFieldsArray = [];
    foreach ($UsedProductFields as $productField) {
      $UsedProductFieldsArray[] = $productField['field_name'];
    }

    return ([
      'tabIsCore' => $tabIsCore,
      'productTypes' => $productTypeNames,
      'usedProductFields' => $UsedProductFieldsArray
    ]);
  }

  /**
   *
   * @global string $db
   * @return array
   */
  public function updateTabSortOrder()
  {
    global $db;
    $data = new objectInfo($_POST);
    $returnData = [];
    foreach ($data->tabSort as $sortOrder) {
      $returnData[] = $sortOrder;
      $updateSortOrderQuery = "UPDATE " . TABLE_PRODUCT_TABS . "
                               SET sort_order = " . (int)$sortOrder['sort_order'] . "
                               WHERE id = " . (int)$sortOrder['id'];
      $db->Execute($updateSortOrderQuery);
    }
    return(['Sort' => $returnData]);
  }

  /**
   * Returns the message stack
   * @global type $messageStack
   * @return array
   */
  public function messageStack()
  {
    global $messageStack;
    if ($messageStack->size > 0) {
      return(['modalMessageStack' => $messageStack->output()]);
    }
  }

}
