<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Returns all the fields that are assigned to a specific tab for a product type
 * @param integer $productType <p>The product type id</p>
 * @param integer $tabId <p>The tab id</p>
 * @return aray <p>Returns all fields that belong to a specific tab</p>
 */
function getFieldsInTab($productType, $tabId)
{
  global $db;

  $fieldsToProductTypeTabQuery = "SELECT *
                                  FROM " . TABLE_PRODUCT_FIELDS_TO_TYPE . "
                                  WHERE product_type = " . (int)$productType . "
                                  AND tab_id = " . (int)$tabId . "
                                  ORDER BY tab_id, sort_order";
  $fieldsToProductTypeTab = $db->Execute($fieldsToProductTypeTabQuery);

  $fields = [];
  foreach ($fieldsToProductTypeTab as $fieldToProductTypeTab) {
    $fields[] = [
      'productTypeId' => $fieldToProductTypeTab['product_type'],
      'fieldName' => $fieldToProductTypeTab['field_name'],
      'sortOrder' => $fieldToProductTypeTab['sort_order'],
      'tabId' => $fieldToProductTypeTab['tab_id'],
      'showInFrontend' => $fieldToProductTypeTab['show_in_frontend']];
  }
  return $fields;
}

/**
 * Returns all the fields that are assigned to a specific product type
 * @param array $array1 <p>The haystack</p>
 * @param array $array2 <p>The needle</p>
 * @return array <p>Returns all fields that belong to a specific product type</p>
 */
function getAvailableFields($array1, $array2)
{
  // loop through each item on the first array
  foreach ($array1 as $key => $row) {
    // loop through array 2 and compare
    foreach ($array2 as $row2) {
      if ($row == $row2['fieldName']) {
        // if we found a match unset and break out of the loop
        unset($array1[$key]);
        break;
      }
    }
  }
  return array_values($array1);
}

/**
 * Returns the value core of a field
 * @param integer $id <p>The field id</p>
 * @return integer <p>Returns the value of the core field (1 or 0)</p>
 */
function fieldIsCore($id)
{
  global $db;
  $fieldIsCoreQuery = "SELECT core
                       FROM " . TABLE_PRODUCT_FIELDS . "
                       WHERE id = " . (int)$id;
  $fieldIsCore = $db->Execute($fieldIsCoreQuery);

  return $fieldIsCore->fields['core'];
}

/**
 * 
 * @global array $db
 * @return array Returns all defined tabs
 */
function getAllTabs()
{
  global $db;
  $availableTabsQuery = "SELECT pt.*, ptn.tab_name
                         FROM " . TABLE_PRODUCT_TABS . " pt
                         LEFT JOIN " . TABLE_PRODUCT_TABS_NAMES . " ptn ON pt.id = ptn.id
                           AND language_id = " . $_SESSION['languages_id'] . "
                         ORDER BY pt.sort_order, ptn.tab_name";
  $availableTabs = $db->Execute($availableTabsQuery);

  $availableTabsArray = [];

  foreach ($availableTabs as $tab) {
    $availableTabsArray[] = [
      'id' => $tab['id'],
      'tabName' => $tab['tab_name'],
      'sortOrder' => $tab['sort_order'],
      'core' => $tab['core'],
      'productType' => (!empty($tab['product_type_id']) ? explode('|', $tab['product_type_id']) : [])
    ];
  }
  return $availableTabsArray;
}

/**
 * 
 * @param integer $productType
 * @return array returns all available tabs for a product type
 */
function getTabsInType($productType = '1')
{

  $availableTabs = getAllTabs();
  $i = 0;
  foreach ($availableTabs as $key => $tab) {
    if (!in_array($productType, $tab['productType'])) {
      unset($availableTabs[$key]);
    }
    $i++;
  }

  $availableTabsArray = array_values($availableTabs);
  return $availableTabsArray;
}

/**
 * 
 * @global array $db
 * @return array <p>The available product types</p>
 */
function getProductTypes()
{
  global $db;
  $productTypesQuery = "SELECT * FROM " . TABLE_PRODUCT_TYPES;
  $productTypes = $db->Execute($productTypesQuery);
  $productTypesArray = [];
  foreach ($productTypes as $productType) {
    $productTypesArray[] = [
      'type_id' => $productType['type_id'],
      'type_name' => $productType['type_name'],
      ];
  }
  return $productTypesArray;
}

/**
 * 
 * @global array $db
 * @param integer $id <p>tab ID</p>
 * @param integer $languageId Language ID
 * @return string The tab name
 */
function getTabName($id, $languageId)
{
  global $db;
  $tabNameQuery = "SELECT tab_name
                   FROM " . TABLE_PRODUCT_TABS_NAMES . "
                   WHERE id = " . (int)$id . "
                   AND language_id = " . (int)$languageId;
  $tabName = $db->Execute($tabNameQuery);
  return $tabName->fields['tab_name'];
}