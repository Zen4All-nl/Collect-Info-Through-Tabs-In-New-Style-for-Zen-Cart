<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Returns the name of a product field
 * @param integer $id <p>The field id</p>
 * @return string <p>The name of a product field</p>
 */
function getFieldName($id) {
  global $db;
  $fieldNameQuery = "SELECT name, description
                     FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                     WHERE id = " . (int)$id . "
                     ORDER BY name";
  $fieldName = $db->Execute($fieldNameQuery);

  $name = (!empty($fieldName->fields['description']) ? $fieldName->fields['description'] : $fieldName->fields['name']);
  return $name;
}

/**
 * Returns all the fields that are assigned to a specific tab for a product type
 * @param integer $productType <p>The product type id</p>
 * @param integer $tabId <p>The tab id</p>
 * @return aray <p>Returns all fields that belong to a specific tab</p>
 */
function getFieldsInTab($productType, $tabId) {
  global $db;

  $fieldsToProductTypeTabQuery = "SELECT *
                             FROM " . TABLE_PRODUCT_TYPE_FIELDS_TO_TYPE . "
                             WHERE product_type_id = " . (int)$productType . "
                               AND tab_id = " . $tabId . "
                             ORDER BY tab_id, sort_order";
  $fieldsToProductTypeTab = $db->Execute($fieldsToProductTypeTabQuery);

  $fields = [];
  foreach ($fieldsToProductTypeTab as $fieldToProductTypeTab) {
    $fields[] = [
      'productTypeId' => $fieldToProductTypeTab['product_type_id'],
      'fieldId' => $fieldToProductTypeTab['field_id'],
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
function getAvailableFields($array1, $array2) {
  // loop through each item on the first array
  foreach ($array1 as $key => $row) {
    // loop through array 2 and compare
    foreach ($array2 as $row2) {
      if ($row['id'] == $row2['fieldId']) {
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
function fieldIsCore($id) {
  global $db;
  $fieldIsCoreQuery = "SELECT core
                       FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                       WHERE id = " . (int)$id;
  $fieldIsCore = $db->Execute($fieldIsCoreQuery);

  return $fieldIsCore->fields['core'];
}

/**
 * 
 * @global type $db
 * @return array
 */
function getTabs($productType = '1') {
  global$db;
  $availableTabsQuery = "SELECT *
                         FROM " . TABLE_PRODUCT_TABS . "
                         ORDER BY sort_order, define";
  $availableTabs = $db->Execute($availableTabsQuery);

  $availableTabsArray = [];
  $i = 0;
  foreach ($availableTabs as $tab) {
    $availableTabsArray[$i] = [
      'id' => $tab['id'],
      'define' => $tab['define'],
      'sortOrder' => $tab['sort_order'],
      'core' => $tab['core'],
      'productType' => explode('|', $tab['product_type_id'])
    ];
    if (!in_array($productType, $availableTabsArray[$i]['productType'])) {
      unset($availableTabsArray[$i]);
    }
    $i++;
  }

  return $availableTabsArray;
}

/**
 * 
 * @param string $name
 * @param string $type
 * @param integer $length
 * @param string $default_value
 * @return string
 */
function setSqlTypeInformation($name, $type, $length, $default_value = '') {
  $data = '';
  $data .= $name;
  if ($type == '1') {
    ($length == '' ? '64' : $length);
    $data .= ' VARCHAR(' . (int)$length . ')';
    $data .= ($default_value == '' ? ' NULL DEFAULT NULL' : 'NOT NULL DEFAULT ' . $default_value);
  } elseif ($type == '2') {
    $data .= ' TEXT';
    $data .= ($default_value == '' ? ' NULL DEFAULT NULL' : 'NOT NULL DEFAULT ' . $default_value);
  } elseif ($type == '3') {
    ($length == '' ? '11' : $length);
    $data .= ' INT(' . (int)$length . ')';
    $data .= ($default_value == '' ? ' NOT NULL DEFAULT \'0\'' : 'NOT NULL DEFAULT \'' . $default_value . '\'');
  } elseif ($type == '4') {
    ($length == '' ? '10,0' : $length);
    $data .= ' DECIMAL(' . (int)$length . ')';
    $data .= ($default_value == '' ? ' NOT NULL DEFAULT \'0.0\'' : 'NOT NULL DEFAULT \'' . $default_value . '\'');
  } elseif ($type == '5') {
    $data .= ' FLOAT';
    $data .= ($default_value == '' ? ' NOT NULL DEFAULT \'0\'' : 'NOT NULL DEFAULT \'' . $default_value . '\'');
  } elseif ($type == '6') {
    ($length == '' ? '11' : $length);
    $data .= ' INT(' . (int)$length . ')';
    $data .= ($default_value == '' ? ' NOT NULL DEFAULT \'0\'' : 'NOT NULL DEFAULT \'' . $default_value . '\'');
  } elseif ($type == '7') {
    ($length == '' ? '4' : $length);
    $data .= ' TINYINT(' . (int)$length . ')';
    $data .= ($default_value == '' ? ' NOT NULL DEFAULT \'0\'' : 'NOT NULL DEFAULT \'' . $default_value . '\'');
  } elseif ($type == '8') {
    ($length == '' ? '4' : $length);
    $data .= ' TINYINT(' . (int)$length . ')';
    $data .= ($default_value == '' ? ' NOT NULL DEFAULT \'0\'' : 'NOT NULL DEFAULT \'' . $default_value . '\'');
  } elseif ($type == '9') {
    $data .= ' DATETIME';
    $data .= ($default_value == '' ? ' NOT NULL DEFAULT \'0001-01-01 00:00:00\'' : 'NOT NULL DEFAULT \'' . $default_value . '\'');
  }

  return $data;
}