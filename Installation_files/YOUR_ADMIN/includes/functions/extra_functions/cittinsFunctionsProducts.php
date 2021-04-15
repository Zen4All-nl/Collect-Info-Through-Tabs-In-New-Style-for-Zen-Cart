<?php

/*
 * @package admin
 * @copyright Copyright 2008-2021 Zen4All
 * @license https://github.com/Zen4All-nl/Collect-Info-Through-Tabs-In-New-Style-for-Zen-Cart/blob/stable/LICENSE GNU Public License V2.0
 * @version Cittins 2.0.0 by Zen4All
 */

/**
 * search directories for the needed files
 * @param string $dir
 * @param string $prefix
 * @return array <p>directories and files</p>
 */
function recursiveDirList(string $dir, string $prefix = '')
{
  $dir_1 = rtrim($dir, DIRECTORY_SEPARATOR);
  $result = [];

  foreach (glob($dir_1 . DIRECTORY_SEPARATOR . '*', GLOB_MARK) as &$f) {
    if (substr($f, -1) === DIRECTORY_SEPARATOR) {
      $result = array_merge($result, recursiveDirList($f, $prefix . basename($f) . DIRECTORY_SEPARATOR));
    } else {
      $result[] = $prefix . basename($f);
    }
  }

  return $result;
}

if (!function_exists('zen_html_button')) {

  /**
   * Returns the html code for (bootsttrap) buttons
   * @param string $type The button type: submit, button
   * @param string $text The text on the button: Submit, Cancel, Save
   * @param string $class The Button color: default, primary, succes, info, warning, danger, link
   * @param string $parameters Things like an id, or a name
   * @param string $size The button size: xs, sm, lg For normal size leave empty
   * @return string The html button code
   */
  function zen_html_button(string $type = 'button', string $text = '', string $class = 'default', string $parameters = '', string $size = '') :string
  {
    $button = '<button type="' . zen_output_string_protected($type) . '" class="btn btn-' . zen_output_string_protected($class) . '';
    if (zen_not_null($size)) {
      $button .= ' btn-' . zen_output_string_protected($size);
    }
    $button .= '"';
    if (zen_not_null($parameters)) {
      $button .= ' ' . $parameters;
    }
    $button .= '>' . $text . '</button>';
    return $button;
  }

}

/**
 * Return all files with a certain name in a directory including sub-directories
 * @param string $path <p>
 * This parameter specifies the directory you wish to search trough.
 * It can reference a local directory
 * </p>
 * @param string $file <p>
 * This parameter specifies the file you wish to retrieve information
 * about. It can reference a local file
 * </p>
 * @return array <p>An array wich contains the retrieved files, including their paths</p>
 */
function dirList(string $path, string $file)
{
  $dirs = glob($path . '*', GLOB_ONLYDIR);
  $files = array();
//--- search through each folder for the file
//--- append results to $files
  foreach ($dirs as $d) {
    $f = glob($d . '/' . $file);
    if (count($f)) {
      $files = array_merge($files, $f);
    }
  }
  if (count($files)) {
    foreach ($files as $f) {
      $results[] = $f;
    }
    asort($results);
  } else {
    $results = '';
  }

  return $results;
}

/**
 * Returns an array of data needed for the product info
 * @param string $directory <p>Directory to be searched for files</p>
 * @return array
 */
function dirListProductFields(string $directory)
{
  // create an array to hold directory list
  $results = array();

  if (is_dir($directory)) {
    // create a handler for the directory
    if ($handler = opendir($directory)) {
      // keep going until all files in directory have been read
      while ($file = readdir($handler)) {
        // if $file isn't this directory or its parent, 
        // add it to the results array
        if ($file != '.' && $file != '..')
          $results[] = $file;
      }
      asort($results);
      // tidy up: close the handler
      closedir($handler);
      // done!
    }
  }

  return $results;
}

/**
 * Get the contents of the image folder
 * @param int $product_id
 * @param string $products_image
 * @return array
 */
function getAdditionalImages(int $product_id, string $products_image)
{
  $result['products_image'] = DIR_WS_IMAGES . $products_image;

  // prepare image name
  $products_image_extension = substr($products_image, strrpos($products_image, '.'));
  $products_image_base = str_replace($products_image_extension, '', $products_image);

  $images_array = array();

  if (strrpos($products_image, '/')) {
    $products_image_match = substr($products_image, strrpos($products_image, '/') + 1);
    $products_image_match = str_replace($products_image_extension, '', $products_image_match);
    $products_image_base = $products_image_match;
  }

  $products_image_directory = str_replace($products_image, '', substr($products_image, strrpos($products_image, '/')));
  if ($products_image_directory != '') {
    $products_image_directory = DIR_WS_IMAGES . str_replace($products_image_directory, '', $products_image) . '/';
  } else {
    $products_image_directory = DIR_WS_IMAGES;
  }
  $image_count = 1;
  $search_directory = DIR_FS_CATALOG . $products_image_directory;
  $glob_search = $search_directory . $products_image_base . '_[0-9][0-9]' . $products_image_extension;
  $files = glob($glob_search);
  $result['glob_search'] = $glob_search;
  $result['image_base'] = $products_image_base;
  $result['extension'] = $products_image_extension;
  $result['search_dir'] = $search_directory;
  $images_array[] = array('filename' => HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG . $products_image_directory . $products_image_base . $products_image_extension);
  $result['files'] = $files;
  if (is_array($files) && !empty($files)) {
    foreach ($files as $file) {
      $image_count++;
      $image_suffix_number = str_replace($search_directory . $products_image_base . '_', '', $file);
      $image_suffix_number = str_replace($products_image_extension, '', $image_suffix_number);
      $images_array[] = array(
        //  'filepath' => str_replace(DIR_FS_CATALOG, HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG, $file),
        'filename' => str_replace(DIR_FS_CATALOG, HTTPS_CATALOG_SERVER . DIR_WS_HTTPS_CATALOG, $file),
        'count' => $image_count,
        'suffix_number' => $image_suffix_number
      );
    }
  }
  $result['last_image_suffix'] = (int)$image_suffix_number;
  $next_image_suffix = (int)$image_suffix_number + 1;
  $text_image_suffix = str_pad($next_image_suffix, 2, '0', STR_PAD_LEFT);
  $new_img_dir = str_replace(DIR_WS_IMAGES, '', $products_image_directory);
  $result['new_filename'] = $products_image_base . '_' . $text_image_suffix . $products_image_extension;
  $result['destination'] = $new_img_dir;
  $result['next_image_number'] = $image_count;
  $result['success'] = 'Success: Found Images';
  $result['images'] = $images_array;

  return $result;
}

/**
 * Return the information for a product field
 * @global object $db
 * @param int $id <p>
 * This parameter specifies the id from the field you want queried.
 * </p>
 * @return array <p>An array which contains the retrieved field information</p>
 */
function getFieldInformation(int $id)
{
  global $db;
  $fieldQuery = "SELECT id, field_name, description, core
                 FROM " . TABLE_PRODUCT_TYPE_FIELDS . "
                 WHERE id = " . $id;
  $field = $db->Execute($fieldQuery);
  $fieldInformation = [
    'id' => $field->fields['id'],
    'field_name' => $field->fields['field_name'],
    'description' => $field->fields['description'],
    'core' => $field->fields['core']
  ];
  return $fieldInformation;
}

/**
 * 
 * @global object $db
 * @param int $ProductTypeId
 * @return array
 */
function getProductTypeInfo(int $ProductTypeId)
{
  global $db;
  $productTypeInfoQuery = "SELECT pt.*, COUNT(p.products_id) AS products_count
                           FROM " . TABLE_PRODUCT_TYPES . " pt
                           LEFT JOIN " . TABLE_PRODUCTS . " p ON p.products_type = pt.type_id
                           WHERE pt.type_id = " . (int)$ProductTypeId;
  $productTypeInfo = $db->Execute($productTypeInfoQuery);

  return $productTypeInfoArray = new objectInfo($productTypeInfo->fields);
}

/**
 * 
 * @global object $db
 * @param int $productId
 */
function zen4allCheckProductTables(int $productId)
{
  global $db;
  $languages = zen_get_languages();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $checkMetaTagsProductsDescriptionTable = $db->Execute("SELECT products_id
                                                           FROM " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . "
                                                           WHERE products_id = " . (int)$productId . "
                                                           AND language_id = " . (int)$languages[$i]['id']);
    if ($checkMetaTagsProductsDescriptionTable->RecordCount() < 1) {
      $db->Execute("INSERT INTO " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " (products_id, language_id)
                    VALUES (" . (int)$productId . ", " . (int)$languages[$i]['id'] . ")");
    }
  }
}
